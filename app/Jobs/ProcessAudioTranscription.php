<?php

namespace App\Jobs;

use App\Models\Meeting;
use App\Services\AssemblyAITranscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAudioTranscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900;
    public $tries   = 1;
    public $backoff = 10;

    public function __construct(public Meeting $meeting)
    {
    }

    public function handle(AssemblyAITranscriptionService $service)
    {
        try {
            Log::info("=== JOB STARTED: Meeting {$this->meeting->id} ===");

            // Always refresh from DB to get latest state
            $this->meeting = Meeting::find($this->meeting->id);

            if (!$this->meeting) {
                Log::error("Meeting not found in DB");
                return;
            }

            if (!$this->meeting->audio_file_path) {
                throw new \Exception("No audio file path on meeting ID {$this->meeting->id}");
            }

            Log::info("Audio path: {$this->meeting->audio_file_path}");

            // Check file exists before sending to AssemblyAI
            $fullPath = storage_path('app/public/' . $this->meeting->audio_file_path);
            if (!file_exists($fullPath)) {
                throw new \Exception("Audio file not found at: {$fullPath}");
            }

            Log::info("File size: " . round(filesize($fullPath) / 1024 / 1024, 2) . " MB");

            // Update status
            $this->meeting->update(['processing_status' => 'transcribing']);

            // Transcribe using AssemblyAI
            $result = $service->transcribe($this->meeting->audio_file_path);

            Log::info("Transcription returned " . count($result['segments']) . " segments");

            // Delete any old transcript first
            $this->meeting->transcript()->delete();

            // Save transcript
            $transcript = $this->meeting->transcript()->create([
                'full_text'  => $result['full_text'],
                'segments'   => $result['segments'],
                'language'   => $result['language'],
                'word_count' => str_word_count($result['full_text']),
            ]);

            Log::info("Transcript saved, ID: {$transcript->id}, Words: {$transcript->word_count}");

            // Update duration
            if (!$this->meeting->duration && !empty($result['duration'])) {
                $this->meeting->update(['duration' => (int) $result['duration']]);
            }

            $this->meeting->update(['processing_status' => 'transcribed']);
            Log::info("=== TRANSCRIPTION DONE: Meeting {$this->meeting->id} ===");

            // Dispatch next job
            GenerateMeetingSummary::dispatch($this->meeting->fresh());

        } catch (\Throwable $e) {
            Log::error("TRANSCRIPTION JOB FAILED: " . $e->getMessage());
            Log::error("File: " . $e->getFile() . " Line: " . $e->getLine());

            Meeting::where('id', $this->meeting->id)->update([
                'processing_status' => 'failed',
                'error_message'     => $e->getMessage(),
            ]);

            $this->fail($e);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error("JOB PERMANENTLY FAILED: Meeting {$this->meeting->id}");
        Log::error($exception->getMessage());

        Meeting::where('id', $this->meeting->id)->update([
            'processing_status' => 'failed',
            'error_message'     => $exception->getMessage(),
        ]);
    }
}