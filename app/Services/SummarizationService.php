<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class SummarizationService
{
    public function generateSummary($transcriptText)
    {
        try {
            $prompt = "Please analyze the following meeting transcript and provide:
1. A brief 2-3 sentence summary
2. A detailed summary (150-200 words)
3. Key action items (if any)
4. Key decisions made (if any)
5. Main topics discussed

Transcript:
{$transcriptText}

Return the response in JSON format with keys: brief_summary, summary_text, action_points (array), key_decisions (array), key_topics (array)";

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert at analyzing and summarizing meeting transcripts. Always return valid JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            $content = $response->choices[0]->message->content;
            $data = json_decode($content, true);

            return [
                'brief_summary' => $data['brief_summary'] ?? '',
                'summary_text' => $data['summary_text'] ?? '',
                'action_points' => $data['action_points'] ?? [],
                'key_decisions' => $data['key_decisions'] ?? [],
                'key_topics' => $data['key_topics'] ?? [],
            ];

        } catch (\Exception $e) {
            Log::error('Summarization failed: ' . $e->getMessage());
            throw $e;
        }
    }
}