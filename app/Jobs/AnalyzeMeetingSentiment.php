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
    public $tries   = 1;

    public function __construct(public Meeting $meeting)
    {
    }

    public function handle(SentimentAnalysisService $service)
    {
        try {
            Log::info("=== SENTIMENT JOB STARTED: Meeting {$this->meeting->id} ===");

            // Refresh from DB
            $this->meeting = Meeting::find($this->meeting->id);

            if (!$this->meeting) {
                Log::error("Meeting not found");
                return;
            }

            $transcript = $this->meeting->transcript;

            if (!$transcript) {
                throw new \Exception("Transcript not found for meeting {$this->meeting->id}");
            }

            Log::info("Analyzing sentiment for " . count($transcript->segments) . " segments");

            // Analyze sentiment (no API needed - keyword based)
            $result = $service->analyze($transcript->segments);

            // Delete existing if any
            $this->meeting->sentimentAnalysis()->delete();

            // Save sentiment
            $this->meeting->sentimentAnalysis()->create([
                'overall_sentiment'   => $result['overall_sentiment'],
                'positive_score'      => $result['positive_score'],
                'negative_score'      => $result['negative_score'],
                'neutral_score'       => $result['neutral_score'],
                'sentiment_breakdown' => $result['sentiment_breakdown'],
                'chart_data'          => $result['chart_data'],
            ]);

            // Mark as COMPLETED - this is the final step!
            $this->meeting->update(['processing_status' => 'completed']);

            if ($this->meeting->source === 'whatsapp' && $this->meeting->whatsapp_from) {
                SendWhatsAppReply::dispatch($this->meeting->fresh());
            }

            Log::info("=== ALL PROCESSING COMPLETE: Meeting {$this->meeting->id} ===");

        } catch (\Throwable $e) {
            Log::error("SENTIMENT JOB FAILED: " . $e->getMessage());
            Log::error("File: " . $e->getFile() . " Line: " . $e->getLine());

            Meeting::where('id', $this->meeting->id)->update([
                'processing_status' => 'failed',
                'error_message'     => 'Sentiment failed: ' . $e->getMessage(),
            ]);

            $this->fail($e);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error("SENTIMENT JOB PERMANENTLY FAILED: Meeting {$this->meeting->id}");

        Meeting::where('id', $this->meeting->id)->update([
            'processing_status' => 'failed',
            'error_message'     => 'Sentiment failed: ' . $exception->getMessage(),
        ]);
    }
}