<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class SentimentAnalysisService
{
    public function analyze($transcriptSegments)
    {
        try {
            Log::info('SentimentAnalysisService: analyzing sentiment using OpenAI');

            // Collect all text from segments
            $allText = '';
            if (is_array($transcriptSegments)) {
                foreach ($transcriptSegments as $segment) {
                    $allText .= ' ' . (is_array($segment) ? ($segment['text'] ?? '') : ($segment->text ?? ''));
                }
            }

            $allText = trim((string) $allText);

            if ($allText === '') {
                return $this->defaultResult();
            }

            // Limit text length to stay within token limits
            $maxChars = 6000;
            if (mb_strlen($allText) > $maxChars) {
                $allText = mb_substr($allText, 0, $maxChars) . "\n\n[Transcript truncated for sentiment analysis]";
            }

            if (!config('openai.api_key')) {
                throw new \Exception('OpenAI API key is not configured');
            }

            $model = config('services.openai.model', 'gpt-4o-mini');

            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an assistant that performs sentiment analysis on meeting transcripts. '
                            . 'Always respond with a single JSON object and nothing else.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Analyze the overall sentiment of the following meeting transcript.\n\n"
                            . "Respond ONLY with valid JSON (no markdown, no commentary) with the following exact structure:\n"
                            . "{\n"
                            . "  \"overall_sentiment\": \"positive\" | \"negative\" | \"neutral\" | \"mixed\",\n"
                            . "  \"positive_score\": number,  // 0-100\n"
                            . "  \"negative_score\": number,  // 0-100\n"
                            . "  \"neutral_score\": number,   // 0-100\n"
                            . "  \"sentiment_breakdown\": [   // optional extra detail\n"
                            . "    {\"label\": string, \"score\": number}\n"
                            . "  ]\n"
                            . "}\n\n"
                            . "Ensure that positive_score + negative_score + neutral_score is approximately 100.\n\n"
                            . "Transcript:\n"
                            . $allText,
                    ],
                ],
                'temperature' => 0.2,
            ]);

            $content = $response->choices[0]->message->content ?? null;

            if (!$content) {
                throw new \Exception('Empty response from OpenAI sentiment analysis');
            }

            $content = trim($content);
            $data = json_decode($content, true);

            if (!is_array($data)) {
                Log::warning('OpenAI sentiment returned non-JSON response, attempting to extract JSON');
                if (preg_match('/\{.*\}/s', $content, $matches)) {
                    $data = json_decode($matches[0], true);
                }
            }

            if (!is_array($data)) {
                throw new \Exception('Unable to decode JSON from OpenAI sentiment response');
            }

            $overall   = (string) ($data['overall_sentiment'] ?? 'neutral');
            $posScore  = (float) ($data['positive_score'] ?? 0.0);
            $negScore  = (float) ($data['negative_score'] ?? 0.0);
            $neutScore = (float) ($data['neutral_score'] ?? max(0, 100 - $posScore - $negScore));

            // Normalize scores so they roughly sum to 100
            $sum = $posScore + $negScore + $neutScore;
            if ($sum > 0) {
                $factor   = 100 / $sum;
                $posScore = round($posScore * $factor, 1);
                $negScore = round($negScore * $factor, 1);
                $neutScore = round($neutScore * $factor, 1);
            }

            Log::info("Sentiment (OpenAI): {$overall} | Pos: {$posScore}% | Neg: {$negScore}% | Neu: {$neutScore}%");

            return [
                'overall_sentiment'   => $overall,
                'positive_score'      => $posScore,
                'negative_score'      => $negScore,
                'neutral_score'       => $neutScore,
                'sentiment_breakdown' => $data['sentiment_breakdown'] ?? [],
                'chart_data'          => [
                    'labels' => ['Positive', 'Negative', 'Neutral'],
                    'values' => [$posScore, $negScore, $neutScore],
                ],
            ];

        } catch (\Exception $e) {
            Log::error('Sentiment analysis failed: ' . $e->getMessage());
            return $this->defaultResult();
        }
    }

    private function defaultResult(): array
    {
        return [
            'overall_sentiment'   => 'neutral',
            'positive_score'      => 50.0,
            'negative_score'      => 10.0,
            'neutral_score'       => 40.0,
            'sentiment_breakdown' => [],
            'chart_data'          => [
                'labels' => ['Positive', 'Negative', 'Neutral'],
                'values' => [50.0, 10.0, 40.0],
            ],
        ];
    }
}