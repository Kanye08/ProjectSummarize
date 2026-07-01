<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessAudioTranscription;
use App\Models\Meeting;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Meta's webhook verification handshake.
     * Note: PHP converts dots in query string keys to underscores,
     * so "hub.mode" arrives as "hub_mode" etc.
     */
    public function verify(Request $request)
    {
        if (
            $request->query('hub_mode') === 'subscribe'
            && $request->query('hub_verify_token') === config('services.whatsapp.verify_token')
        ) {
            return response($request->query('hub_challenge'), 200);
        }

        return response('Forbidden', 403);
    }

    public function receive(Request $request, WhatsAppService $whatsapp)
    {
        if (!$this->signatureIsValid($request)) {
            Log::warning('WhatsApp webhook signature mismatch — rejecting payload');
            return response('Invalid signature', 403);
        }

        $message = $request->input('entry.0.changes.0.value.messages.0');

        if (!$message || ($message['type'] ?? null) !== 'audio') {
            // Not a voice note - acknowledge and ignore (status updates, text messages, etc.)
            return response()->json(['status' => 'ignored']);
        }

        try {
            $from    = $message['from'];
            $mediaId = $message['audio']['id'];

            $audioPath = $whatsapp->downloadMedia($mediaId);

            $botUser = User::firstOrCreate(
                ['email' => 'whatsapp@summaraize.local'],
                [
                    'name'     => 'WhatsApp Bot',
                    'username' => 'whatsapp-bot',
                    'password' => Hash::make(str()->random(40)),
                    'email_verified_at' => now(),
                ]
            );

            $meeting = Meeting::create([
                'user_id'           => $botUser->id,
                'title'             => 'WhatsApp voice note — ' . now()->format('M j, Y g:i A'),
                'start_time'        => now(),
                'location'          => 'WhatsApp',
                'status'            => 'completed',
                'source'            => 'whatsapp',
                'whatsapp_from'     => $from,
                'audio_file_path'   => $audioPath,
                'audio_file_name'   => basename($audioPath),
                'audio_file_size'   => \Illuminate\Support\Facades\Storage::disk('public')->size($audioPath),
                'audio_format'      => pathinfo($audioPath, PATHINFO_EXTENSION),
                'processing_status' => 'uploaded',
            ]);

            ProcessAudioTranscription::dispatch($meeting);

            $whatsapp->sendText($from, "Got your voice note! 🎙️ Transcribing and summarizing now — I'll send the results here shortly.");

            Log::info("WhatsApp meeting {$meeting->id} created from {$from}, job dispatched");

        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook processing failed: ' . $e->getMessage());
        }

        return response()->json(['status' => 'ok']);
    }

    protected function signatureIsValid(Request $request): bool
    {
        $secret = config('services.whatsapp.app_secret');

        if (!$secret) {
            // No secret configured (e.g. local dev before Meta app is set up) - skip check.
            return true;
        }

        $signatureHeader = $request->header('X-Hub-Signature-256', '');
        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signatureHeader);
    }
}
