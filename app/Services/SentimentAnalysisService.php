<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class SentimentAnalysisService
{
    public function analyze($transcriptSegments)
    {
        try {
            $transcriptText = collect($transcriptSegments)
                ->pluck('text')
                ->implode(' ');

            $prompt = "Analyze the sentiment of this meeting transcript. Provide:
1. Overall sentiment (positive, negative, neutral, or mixed)
2. Percentage scores for positive, negative, and neutral sentiment (must total 100)
3. Sentiment breakdown by segment

Transcript:
{$transcriptText}

Return JSON with: overall_sentiment, positive_score, negative_score, neutral_score, sentiment_breakdown (array of {segment_id, sentiment, score})";

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a sentiment analysis expert. Always return valid JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            $data = json_decode($response->choices[0]->message->content, true);

            return [
                'overall_sentiment' => $data['overall_sentiment'] ?? 'neutral',
                'positive_score' => $data['positive_score'] ?? 0,
                'negative_score' => $data['negative_score'] ?? 0,
                'neutral_score' => $data['neutral_score'] ?? 0,
                'sentiment_breakdown' => $data['sentiment_breakdown'] ?? [],
                'chart_data' => $this->prepareChartData($data),
            ];

        } catch (\Exception $e) {
            Log::error('Sentiment analysis failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function prepareChartData($data)
    {
        return [
            'labels' => ['Positive', 'Negative', 'Neutral'],
            'values' => [
                $data['positive_score'] ?? 0,
                $data['negative_score'] ?? 0,
                $data['neutral_score'] ?? 0,
            ],
        ];
    }
}