<?php

namespace App\Services;

use AssemblyAI\Client as AssemblyAI;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AssemblyAITranscriptionService
{
    public function transcribe($audioPath)
    {
        try {
            Log::info("Starting AssemblyAI transcription for: {$audioPath}");
            
            $fullPath = storage_path('app/public/' . $audioPath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception("Audio file not found");
            }

            // Initialize AssemblyAI client
            $client = new AssemblyAI(env('ASSEMBLYAI_API_KEY'));

            // Upload file
            Log::info("Uploading file to AssemblyAI...");
            $uploadUrl = $client->files()->upload($fullPath);

            // Submit for transcription
            Log::info("Submitting for transcription...");
            $transcript = $client->transcripts()->submit([
                'audio_url' => $uploadUrl,
                'speaker_labels' => true, // Enable speaker detection
            ]);

            // Wait for completion
            Log::info("Waiting for transcription to complete...");
            $transcript = $client->transcripts()->waitUntilReady($transcript->id);

            // Process response
            $segments = [];
            $fullText = $transcript->text;

            if (isset($transcript->words)) {
                $currentSegment = [];
                $segmentId = 0;
                $segmentStart = 0;

                foreach ($transcript->words as $word) {
                    $currentSegment[] = $word->text;
                    
                    // Create segment every ~10 seconds or 50 words
                    if (count($currentSegment) >= 50 || 
                        (isset($word->end) && $word->end - $segmentStart >= 10000)) {
                        
                        $segments[] = [
                            'id' => $segmentId++,
                            'text' => implode(' ', $currentSegment),
                            'start' => $segmentStart / 1000, // Convert ms to seconds
                            'end' => ($word->end ?? 0) / 1000,
                            'speaker' => $word->speaker ?? null,
                        ];
                        
                        $currentSegment = [];
                        $segmentStart = $word->end ?? 0;
                    }
                }

                // Add remaining words
                if (count($currentSegment) > 0) {
                    $segments[] = [
                        'id' => $segmentId,
                        'text' => implode(' ', $currentSegment),
                        'start' => $segmentStart / 1000,
                        'end' => ($transcript->audio_duration ?? 0),
                        'speaker' => null,
                    ];
                }
            } else {
                $segments[] = [
                    'id' => 0,
                    'text' => $fullText,
                    'start' => 0,
                    'end' => $transcript->audio_duration ?? 0,
                    'speaker' => null,
                ];
            }

            return [
                'full_text' => $fullText,
                'segments' => $segments,
                'language' => 'en', // AssemblyAI auto-detects
                'duration' => $transcript->audio_duration ?? 0,
            ];

        } catch (\Exception $e) {
            Log::error('AssemblyAI transcription failed: ' . $e->getMessage());
            throw $e;
        }
    }
}