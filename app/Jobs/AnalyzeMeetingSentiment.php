<?php

namespace App\Jobs;

use App\Models\Meeting;
use App\Services\SentimentAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeMeetingSentiment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;
    
    public function __construct(public Meeting $meeting)
    {
    }

    public function handle(SentimentAnalysisService $service)
    {
        try {
            Log::info("Starting sentiment analysis for meeting {$this->meeting->id}");

            $transcript = $this->meeting->transcript;
            
            if (!$transcript) {
                throw new \Exception('Transcript not found');
            }

            // Analyze sentiment
            $result = $service->analyze($transcript->segments);

            // Create sentiment analysis record
            $this->meeting->sentimentAnalysis()->create($result);

            // Mark as completed
            $this->meeting->update(['processing_status' => 'completed']);

            Log::info("Processing completed for meeting {$this->meeting->id}");

        } catch (\Exception $e) {
            Log::error("Sentiment analysis failed for meeting {$this->meeting->id}: " . $e->getMessage());
            
            $this->meeting->update([
                'processing_status' => 'failed',
                'error_message' => 'Sentiment analysis failed: ' . $e->getMessage(),
            ]);
        }
    }
}
