<?php

namespace App\Jobs;

use App\Models\Meeting;
use App\Services\SummarizationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateMeetingSummary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries   = 1;

    public function __construct(public Meeting $meeting)
    {
    }

    public function handle(SummarizationService $service)
    {
        try {
            Log::info("=== SUMMARY JOB STARTED: Meeting {$this->meeting->id} ===");

            // Refresh from DB
            $this->meeting = Meeting::find($this->meeting->id);

            if (!$this->meeting) {
                Log::error("Meeting not found");
                return;
            }

            $this->meeting->update(['processing_status' => 'summarizing']);

            // Load transcript
            $transcript = $this->meeting->transcript;

            if (!$transcript) {
                throw new \Exception("Transcript not found for meeting {$this->meeting->id}");
            }

            Log::info("Transcript found ({$transcript->word_count} words), generating summary...");

            // Generate summary
            $result = $service->generateSummary($transcript->full_text);

            // Delete existing summary if any
            $this->meeting->summary()->delete();

            // Save summary
            $this->meeting->summary()->create([
                'summary_text'           => $result['summary_text'],
                'brief_summary'          => $result['brief_summary'],
                'action_points'          => $result['action_points'],
                'key_decisions'          => $result['key_decisions'],
                'key_topics'             => $result['key_topics'],
                'participants_mentioned' => $result['participants_mentioned'] ?? [],
            ]);

            Log::info("=== SUMMARY DONE: Meeting {$this->meeting->id} ===");

            // Dispatch next job
            AnalyzeMeetingSentiment::dispatch($this->meeting->fresh());

        } catch (\Throwable $e) {
            Log::error("SUMMARY JOB FAILED: " . $e->getMessage());
            Log::error("File: " . $e->getFile() . " Line: " . $e->getLine());

            Meeting::where('id', $this->meeting->id)->update([
                'processing_status' => 'failed',
                'error_message'     => 'Summary failed: ' . $e->getMessage(),
            ]);

            $this->fail($e);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error("SUMMARY JOB PERMANENTLY FAILED: Meeting {$this->meeting->id}");

        Meeting::where('id', $this->meeting->id)->update([
            'processing_status' => 'failed',
            'error_message'     => 'Summary failed: ' . $exception->getMessage(),
        ]);
    }
}