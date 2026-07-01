<?php

namespace App\Jobs;

use App\Models\Meeting;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppReply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries   = 1;

    public function __construct(public Meeting $meeting)
    {
    }

    public function handle(WhatsAppService $whatsapp)
    {
        if (!$this->meeting->whatsapp_from) {
            return;
        }

        $summary = $this->meeting->summary;

        $message = "✅ Your meeting summary is ready!\n\n";

        $message .= $summary?->brief_summary
            ? "*Summary:*\n{$summary->brief_summary}\n\n"
            : "We couldn't generate a summary for this recording.\n\n";

        if (!empty($summary?->action_points)) {
            $message .= "*Action points:*\n";
            foreach (array_slice($summary->action_points, 0, 5) as $point) {
                $message .= "• {$point}\n";
            }
        }

        $whatsapp->sendText($this->meeting->whatsapp_from, trim($message));

        Log::info("WhatsApp reply sent for meeting {$this->meeting->id}");
    }
}
