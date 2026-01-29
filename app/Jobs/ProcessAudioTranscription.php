<?php

namespace App\Jobs;

use App\Models\Meeting;
use App\Services\AssemblyAITranscriptionService;
use App\Services\TranscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAudioTranscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;
    public $backoff = 60; // Wait 60 seconds between retries

    public function __construct(public Meeting $meeting)
    {
    }

    // FOR ASSEMBLYAI TRANSCRIPTION SERVICE
    public function handle(AssemblyAITranscriptionService $service) 
    {
        try {
            Log::info("=== Starting transcription job for meeting {$this->meeting->id} ===");
            
            if (!$this->meeting->exists) {
                Log::error("Meeting {$this->meeting->id} no longer exists");
                return;
            }

            if (!$this->meeting->audio_file_path) {
                throw new \Exception("No audio file path set for meeting");
            }

            $this->meeting->update(['processing_status' => 'transcribing']);

            // Transcribe audio
            $result = $service->transcribe($this->meeting->audio_file_path);

            // Create transcript record
            $transcript = $this->meeting->transcript()->create([
                'full_text' => $result['full_text'],
                'segments' => $result['segments'],
                'language' => $result['language'],
                'word_count' => str_word_count($result['full_text']),
            ]);

            Log::info("Transcript created with ID: {$transcript->id}");

            if (!$this->meeting->duration && isset($result['duration'])) {
                $this->meeting->update(['duration' => (int)$result['duration']]);
            }

            $this->meeting->update(['processing_status' => 'transcribed']);
            Log::info("=== Transcription completed for meeting {$this->meeting->id} ===");

            // Chain next job
            \App\Jobs\GenerateMeetingSummary::dispatch($this->meeting);

        } catch (\Exception $e) {
            Log::error("=== Transcription failed for meeting {$this->meeting->id} ===");
            Log::error("Error: " . $e->getMessage());
            
            $this->meeting->update([
                'processing_status' => 'failed',
                'error_message' => 'Transcription failed: ' . $e->getMessage(),
            ]);

            $this->fail($e);
        }
    }

    public function failed(\Throwable $exception)
    {
        $this->meeting->update([
            'processing_status' => 'failed',
            'error_message' => 'Transcription failed: ' . $exception->getMessage(),
        ]);
    }

    // FOR OPENAI TRANSCRIPTION SERVICE
    // public function handle(TranscriptionService $service)
    // {
    //     try {
    //         Log::info("=== Starting transcription job for meeting {$this->meeting->id} ===");
            
    //         // Check if meeting still exists
    //         if (!$this->meeting->exists) {
    //             Log::error("Meeting {$this->meeting->id} no longer exists");
    //             return;
    //         }

    //         // Check if audio file exists
    //         if (!$this->meeting->audio_file_path) {
    //             throw new \Exception("No audio file path set for meeting");
    //         }

    //         $this->meeting->update(['processing_status' => 'transcribing']);
    //         Log::info("Updated status to transcribing");

    //         // Transcribe audio
    //         $result = $service->transcribe($this->meeting->audio_file_path);
            
    //         Log::info("Creating transcript record...");

    //         // Create transcript record
    //         $transcript = $this->meeting->transcript()->create([
    //             'full_text' => $result['full_text'],
    //             'segments' => $result['segments'],
    //             'language' => $result['language'],
    //             'word_count' => str_word_count($result['full_text']),
    //         ]);

    //         Log::info("Transcript created with ID: {$transcript->id}");

    //         // Update meeting duration if not set
    //         if (!$this->meeting->duration && isset($result['duration'])) {
    //             $this->meeting->update(['duration' => (int)$result['duration']]);
    //         }

    //         $this->meeting->update(['processing_status' => 'transcribed']);
    //         Log::info("=== Transcription completed for meeting {$this->meeting->id} ===");

    //         // Chain next job
    //         Log::info("Dispatching summary generation job");
    //         \App\Jobs\GenerateMeetingSummary::dispatch($this->meeting);

    //     } catch (\Exception $e) {
    //         Log::error("=== Transcription failed for meeting {$this->meeting->id} ===");
    //         Log::error("Error: " . $e->getMessage());
    //         Log::error("File: " . $e->getFile() . " Line: " . $e->getLine());
            
    //         $this->meeting->update([
    //             'processing_status' => 'failed',
    //             'error_message' => 'Transcription failed: ' . $e->getMessage(),
    //         ]);

    //         $this->fail($e);
    //     }
    // }
    // public function failed(\Throwable $exception)
    // {
    //     Log::error("Job permanently failed for meeting {$this->meeting->id}");
    //     Log::error("Exception: " . $exception->getMessage());
        
    //     $this->meeting->update([
    //         'processing_status' => 'failed',
    //         'error_message' => 'Transcription failed after ' . $this->tries . ' attempts: ' . $exception->getMessage(),
    //     ]);
    // }
}
