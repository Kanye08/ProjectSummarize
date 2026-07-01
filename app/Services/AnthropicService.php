<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key', '');
        $this->model  = config('services.anthropic.model', 'claude-haiku-4-5');
    }

    /**
     * Send a completion request to Claude and return the response text.
     *
     * @throws \Exception if the API key is missing or the request fails
     */
    public function complete(string $userPrompt, string $systemPrompt = ''): string
    {
        if (!$this->apiKey) {
            throw new \Exception('Anthropic API key is not configured');
        }

        $messages = [];

        if ($systemPrompt !== '') {
            $messages[] = ['role' => 'user', 'content' => $systemPrompt . "\n\n" . $userPrompt];
        } else {
            $messages[] = ['role' => 'user', 'content' => $userPrompt];
        }

        $response = Http::withHeaders([
            'x-api-key'         => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type'      => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model'      => $this->model,
            'max_tokens' => 2048,
            'messages'   => $messages,
        ])->throw();

        $text = $response->json('content.0.text');

        if (!$text) {
            throw new \Exception('Empty response from Anthropic API');
        }

        Log::info("Anthropic ({$this->model}): response received");

        return (string) $text;
    }
}
