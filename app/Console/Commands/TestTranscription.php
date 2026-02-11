<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;

class TestTranscription extends Command
{
    // protected $signature = 'test:transcription';
    // protected $description = 'Test OpenAI transcription setup';

    protected $signature = 'test:assemblyai';
    protected $description = 'Test AssemblyAI configuration';


    // TEST FOR OPENAI
    // public function handle()
    // {
    //     $this->info('Testing OpenAI Configuration...');
        
    //     // Test 1: API Key
    //     $apiKey = config('openai.api_key');
    //     if (!$apiKey) {
    //         $this->error('❌ OpenAI API key is not set in .env');
    //         return 1;
    //     }
    //     $this->info('✅ API key is configured');
    //     $this->info('   Key: ' . substr($apiKey, 0, 10) . '...');
        
    //     // Test 2: Package Installation
    //     try {
    //         $this->info('✅ OpenAI package is installed');
    //     } catch (\Exception $e) {
    //         $this->error('❌ OpenAI package error: ' . $e->getMessage());
    //         return 1;
    //     }
        
    //     // Test 3: Simple API Call
    //     $this->info('Testing API connection...');
    //     try {
    //         $response = OpenAI::chat()->create([
    //             'model' => 'gpt-3.5-turbo',
    //             'messages' => [
    //                 ['role' => 'user', 'content' => 'Say "API Connected"'],
    //             ],
    //             'max_tokens' => 10,
    //         ]);
            
    //         $this->info('✅ API connection successful!');
    //         $this->info('   Response: ' . $response->choices[0]->message->content);
            
    //     } catch (\Exception $e) {
    //         $this->error('❌ API connection failed: ' . $e->getMessage());
    //         return 1;
    //     }
        
    //     $this->info('');
    //     $this->info('🎉 All tests passed!');
    //     return 0;
    // }

    public function handle()
    {
        $this->info('Testing AssemblyAI Configuration...');
        
        // Test API Key
        $apiKey = env('ASSEMBLYAI_API_KEY');
        if (!$apiKey) {
            $this->error('❌ AssemblyAI API key is not set in .env');
            $this->info('Get your free key at: https://www.assemblyai.com/app');
            return 1;
        }
        $this->info('✅ API key is configured');
        $this->info('   Key: ' . substr($apiKey, 0, 10) . '...');
        
        // Test API Connection
        $this->info('Testing API connection...');
        try {
            $response = Http::withHeaders([
                'authorization' => $apiKey,
            ])->get('https://api.assemblyai.com/v2/transcript');
            
            if ($response->successful()) {
                $this->info('✅ API connection successful!');
                $this->info('');
                $this->info('🎉 AssemblyAI is ready to use!');
                $this->info('Free tier: 3 hours of transcription per month');
                return 0;
            } else {
                $this->error('❌ API connection failed');
                $this->error('Response: ' . $response->body());
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ API connection failed: ' . $e->getMessage());
            return 1;
        }
    }
}