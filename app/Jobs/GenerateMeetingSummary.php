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
    public $timeout = 180;
    public $tries = 3;
    /**
     * Create a new job instance.
     */
    public function __construct(Meeting $meeting)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SummarizationService $service)
    {
        try {
            Log::info("Starting summarization for meeting {$this->meeting->id}");
            
            $this->meeting->update(['processing_status' => 'summarizing']);

            $transcript = $this->meeting->transcript;
            
            if (!$transcript) {
                throw new \Exception('Transcript not found');
            }

            // Generate summary
            $result = $service->generateSummary($transcript->full_text);

            // Create summary record
            $this->meeting->summary()->create($result);

            Log::info("Summarization completed for meeting {$this->meeting->id}");

            // Chain sentiment analysis
            AnalyzeMeetingSentiment::dispatch($this->meeting);

        } catch (\Exception $e) {
            Log::error("Summarization failed for meeting {$this->meeting->id}: " . $e->getMessage());
            
            $this->meeting->update([
                'processing_status' => 'failed',
                'error_message' => 'Summarization failed: ' . $e->getMessage(),
            ]);

            $this->fail($e);
        }
    }
}
