<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WhatsAppService
{
    protected string $accessToken;
    protected string $phoneNumberId;

    public function __construct()
    {
        $this->accessToken   = config('services.whatsapp.access_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
    }

    /**
     * Download a WhatsApp media object by its media ID and store it
     * under the same disk/path convention used for in-app recordings.
     *
     * @return string The stored relative path (e.g. "meetings/audio/whatsapp-xxxx.ogg")
     */
    public function downloadMedia(string $mediaId): string
    {
        $meta = Http::withToken($this->accessToken)
            ->get("https://graph.facebook.com/v21.0/{$mediaId}")
            ->throw()
            ->json();

        $binary = Http::withToken($this->accessToken)
            ->get($meta['url'])
            ->throw()
            ->body();

        $extension = match ($meta['mime_type'] ?? null) {
            'audio/ogg; codecs=opus', 'audio/ogg' => 'ogg',
            'audio/mpeg' => 'mp3',
            'audio/mp4', 'audio/aac' => 'm4a',
            default => 'ogg',
        };

        $path = 'meetings/audio/whatsapp-' . Str::uuid() . '.' . $extension;
        Storage::disk('public')->put($path, $binary);

        Log::info("WhatsApp media {$mediaId} downloaded to {$path}");

        return $path;
    }

    public function sendText(string $to, string $message): void
    {
        $response = Http::withToken($this->accessToken)
            ->post("https://graph.facebook.com/v21.0/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to'                => $to,
                'type'              => 'text',
                'text'              => ['body' => $message],
            ]);

        if ($response->failed()) {
            Log::error('WhatsApp sendText failed: ' . $response->body());
        }
    }
}
