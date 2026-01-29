<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TranscriptionService
{
    // public function transcribe($audioPath)
    // {
    //     try {
    //         // Get file from storage
    //         $audioContent = Storage::get($audioPath);
    //         $tempPath = storage_path('app/temp/' . basename($audioPath));
            
    //         // Ensure temp directory exists
    //         if (!file_exists(dirname($tempPath))) {
    //             mkdir(dirname($tempPath), 0755, true);
    //         }
            
    //         file_put_contents($tempPath, $audioContent);

    //         // Call Whisper API
    //         $response = OpenAI::audio()->transcribe([
    //             'model' => 'whisper-1',
    //             'file' => fopen($tempPath, 'r'),
    //             'response_format' => 'verbose_json',
    //             'timestamp_granularities' => ['segment'],
    //         ]);

    //         // Clean up temp file
    //         unlink($tempPath);

    //         // Process response
    //         $segments = [];
    //         $fullText = '';

    //         if (isset($response->segments)) {
    //             foreach ($response->segments as $index => $segment) {
    //                 $segments[] = [
    //                     'id' => $index,
    //                     'text' => $segment->text,
    //                     'start' => $segment->start,
    //                     'end' => $segment->end,
    //                     'speaker' => null, 
    //                 ];
    //                 $fullText .= $segment->text . ' ';
    //             }
    //         } else {
    //             $fullText = $response->text;
    //             $segments[] = [
    //                 'id' => 0,
    //                 'text' => $fullText,
    //                 'start' => 0,
    //                 'end' => 0,
    //                 'speaker' => null,
    //             ];
    //         }

    //         return [
    //             'full_text' => trim($fullText),
    //             'segments' => $segments,
    //             'language' => $response->language ?? 'en',
    //             'duration' => $response->duration ?? 0,
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error('Transcription failed: ' . $e->getMessage());
    //         throw $e;
    //     }
    // }

    public function transcribe($audioPath)
    {
        try {
            Log::info("Starting transcription for: {$audioPath}");
            
            // Get file from local public storage
            $fullPath = storage_path('app/public/' . $audioPath);
            
            Log::info("Full path: {$fullPath}");
            
            if (!file_exists($fullPath)) {
                Log::error("Audio file not found at: {$fullPath}");
                throw new \Exception("Audio file not found at path: {$fullPath}");
            }

            $fileSize = filesize($fullPath);
            Log::info("File size: " . ($fileSize / 1024 / 1024) . " MB");

            // Check if file is readable
            if (!is_readable($fullPath)) {
                throw new \Exception("Audio file is not readable");
            }

            // Check OpenAI API key
            if (!config('openai.api_key')) {
                throw new \Exception('OpenAI API key is not configured');
            }

            Log::info("Calling Whisper API...");

            // Call Whisper API
            $response = OpenAI::audio()->transcribe([
                'model' => 'whisper-1',
                'file' => fopen($fullPath, 'r'),
                'response_format' => 'verbose_json',
                'timestamp_granularities' => ['segment'],
            ]);

            Log::info("Whisper API response received");

            // Process response
            $segments = [];
            $fullText = '';

            if (isset($response->segments)) {
                foreach ($response->segments as $index => $segment) {
                    $segments[] = [
                        'id' => $index,
                        'text' => $segment->text,
                        'start' => $segment->start,
                        'end' => $segment->end,
                        'speaker' => null,
                    ];
                    $fullText .= $segment->text . ' ';
                }
            } else {
                $fullText = $response->text ?? '';
                $segments[] = [
                    'id' => 0,
                    'text' => $fullText,
                    'start' => 0,
                    'end' => 0,
                    'speaker' => null,
                ];
            }

            Log::info("Transcription successful. Text length: " . strlen($fullText));

            return [
                'full_text' => trim($fullText),
                'segments' => $segments,
                'language' => $response->language ?? 'en',
                'duration' => $response->duration ?? 0,
            ];

        } catch (\Exception $e) {
            Log::error('Transcription failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
}