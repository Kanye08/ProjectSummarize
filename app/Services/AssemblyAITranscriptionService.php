<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssemblyAITranscriptionService
{
    private $apiKey;
    private $baseUrl = 'https://api.assemblyai.com/v2';

    public function __construct()
    {
        $this->apiKey = env('ASSEMBLYAI_API_KEY');
        
        if (!$this->apiKey) {
            throw new \Exception('AssemblyAI API key is not configured');
        }
    }

    public function transcribe($audioPath)
    {
        try {
            Log::info("Starting AssemblyAI transcription for: {$audioPath}");
            
            // Get full file path
            $fullPath = storage_path('app/public/' . $audioPath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception("Audio file not found at: {$fullPath}");
            }

            Log::info("File found, size: " . filesize($fullPath) . " bytes");

            // Step 1: Upload the audio file
            $uploadUrl = $this->uploadFile($fullPath);
            Log::info("File uploaded to: {$uploadUrl}");

            // Step 2: Submit for transcription
            $transcriptId = $this->submitTranscription($uploadUrl);
            Log::info("Transcription submitted, ID: {$transcriptId}");

            // Step 3: Poll for completion
            $transcript = $this->pollTranscription($transcriptId);
            Log::info("Transcription completed!");

            // Step 4: Process the response
            return $this->processTranscript($transcript);

        } catch (\Exception $e) {
            Log::error('AssemblyAI transcription failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function uploadFile($filePath)
    {
        Log::info("Uploading file to AssemblyAI...");
        
        $response = Http::withHeaders([
            'authorization' => $this->apiKey,
        ])->attach(
            'file', 
            file_get_contents($filePath), 
            basename($filePath)
        )->post($this->baseUrl . '/upload');

        if ($response->failed()) {
            throw new \Exception('File upload failed: ' . $response->body());
        }

        $data = $response->json();
        return $data['upload_url'];
    }

    private function submitTranscription($audioUrl)
    {
        Log::info("Submitting transcription request...");
        
        $response = Http::withHeaders([
            'authorization' => $this->apiKey,
            'content-type' => 'application/json',
        ])->post($this->baseUrl . '/transcript', [
            'audio_url' => $audioUrl,
            'speaker_labels' => false, // Enable speaker detection (optional)
            'language_code' => 'en', // Auto-detect language
        ]);

        if ($response->failed()) {
            throw new \Exception('Transcription submission failed: ' . $response->body());
        }

        $data = $response->json();
        return $data['id'];
    }

    private function pollTranscription($transcriptId)
    {
        Log::info("Polling for transcription status...");
        
        $maxAttempts = 120; // 10 minutes max (5 seconds * 120)
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            $response = Http::withHeaders([
                'authorization' => $this->apiKey,
            ])->get($this->baseUrl . "/transcript/{$transcriptId}");

            if ($response->failed()) {
                throw new \Exception('Failed to get transcript status: ' . $response->body());
            }

            $data = $response->json();
            $status = $data['status'];

            Log::info("Transcription status: {$status}");

            if ($status === 'completed') {
                return $data;
            }

            if ($status === 'error') {
                throw new \Exception('Transcription failed: ' . ($data['error'] ?? 'Unknown error'));
            }

            // Wait 5 seconds before next poll
            sleep(5);
            $attempts++;
        }

        throw new \Exception('Transcription timed out after 10 minutes');
    }

    private function processTranscript($data)
    {
        $fullText = $data['text'];
        $segments = [];

        // AssemblyAI provides words, we'll group them into segments
        if (isset($data['words']) && is_array($data['words'])) {
            $currentSegment = [];
            $segmentId = 0;
            $segmentStart = 0;
            $segmentEnd = 0;

            foreach ($data['words'] as $index => $word) {
                $currentSegment[] = $word['text'];
                
                if ($segmentStart === 0) {
                    $segmentStart = $word['start'] / 1000; 
                }
                
                $segmentEnd = $word['end'] / 1000;

                // Create a new segment every 15 seconds or 40 words
                $shouldCreateSegment = (
                    count($currentSegment) >= 40 || 
                    ($segmentEnd - $segmentStart >= 15) ||
                    $index === count($data['words']) - 1 
                );

                if ($shouldCreateSegment && count($currentSegment) > 0) {
                    $segments[] = [
                        'id' => $segmentId++,
                        'text' => implode(' ', $currentSegment),
                        'start' => $segmentStart,
                        'end' => $segmentEnd,
                        'speaker' => null,
                    ];
                    
                    $currentSegment = [];
                    $segmentStart = 0;
                }
            }
        } else {
            // If no words data, create one segment with full text
            $segments[] = [
                'id' => 0,
                'text' => $fullText,
                'start' => 0,
                'end' => ($data['audio_duration'] ?? 0) / 1000,
                'speaker' => null,
            ];
        }

        Log::info("Created " . count($segments) . " segments");

        return [
            'full_text' => $fullText,
            'segments' => $segments,
            'language' => $data['language_code'] ?? 'en',
            'duration' => ($data['audio_duration'] ?? 0) / 1000, 
        ];
    }
}