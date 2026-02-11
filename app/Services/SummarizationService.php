<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class SummarizationService
{
    public function generateSummary($transcriptText)
    {
        try {
            Log::info('SummarizationService: generating summary from transcript using OpenAI');

            $transcriptText = trim((string) $transcriptText);

            if ($transcriptText === '') {
                throw new \Exception('Empty transcript text provided for summarization');
            }

            // Limit transcript size to avoid token overflows
            // Roughly cap to ~8k characters; adjust if needed
            $maxChars = 8000;
            if (mb_strlen($transcriptText) > $maxChars) {
                $transcriptText = mb_substr($transcriptText, 0, $maxChars) . "\n\n[Transcript truncated for summarization]";
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
                        'content' => 'You are an assistant that summarizes meeting transcripts. '
                            . 'Always respond with a single JSON object and nothing else.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "You are given a meeting transcript.\n\n"
                            . "Respond ONLY with valid JSON (no markdown, no commentary) with the following exact structure:\n"
                            . "{\n"
                            . "  \"brief_summary\": string,                 // 1–3 sentences high-level\n"
                            . "  \"summary_text\": string,                  // 3–8 detailed paragraphs\n"
                            . "  \"action_points\": string[],               // bullet-style concise action items\n"
                            . "  \"key_decisions\": string[],               // decisions that were made\n"
                            . "  \"key_topics\": string[],                  // main topics discussed\n"
                            . "  \"participants_mentioned\": string[]       // names or roles of people mentioned\n"
                            . "}\n\n"
                            . "Transcript:\n"
                            . $transcriptText,
                    ],
                ],
                'temperature' => 0.4,
            ]);

            $content = $response->choices[0]->message->content ?? null;

            if (!$content) {
                throw new \Exception('Empty response from OpenAI summarization');
            }

            // Some models may return leading/trailing whitespace
            $content = trim($content);

            $data = json_decode($content, true);

            if (!is_array($data)) {
                Log::warning('OpenAI summarization returned non-JSON response, attempting to extract JSON');
                if (preg_match('/\{.*\}/s', $content, $matches)) {
                    $data = json_decode($matches[0], true);
                }
            }

            if (!is_array($data)) {
                throw new \Exception('Unable to decode JSON from OpenAI summarization response');
            }

            // Build result with safe defaults
            return [
                'brief_summary'          => (string) ($data['brief_summary'] ?? 'Summary unavailable.'),
                'summary_text'           => (string) ($data['summary_text'] ?? ''),
                'action_points'          => array_values($data['action_points'] ?? []),
                'key_decisions'          => array_values($data['key_decisions'] ?? []),
                'key_topics'             => array_values($data['key_topics'] ?? []),
                'participants_mentioned' => array_values($data['participants_mentioned'] ?? []),
            ];

        } catch (\Exception $e) {
            Log::error('Summarization failed: ' . $e->getMessage());

            // Fallback: very simple local summary to avoid fully breaking the flow
            $words   = preg_split('/\s+/', trim((string) $transcriptText));
            $preview = implode(' ', array_slice($words, 0, 60));
            $total   = is_array($words) ? count($words) : 0;

            return [
                'brief_summary'          => 'This meeting covered several discussion points. Automatic AI summary failed, so this is a basic fallback.',
                'summary_text'           => "Fallback summary based on the first part of the transcript ({$total} words read):\n\n"
                                            . $preview
                                            . ($total > 60 ? '...' : ''),
                'action_points'          => [],
                'key_decisions'          => [],
                'key_topics'             => [],
                'participants_mentioned' => [],
            ];
        }
    }
}