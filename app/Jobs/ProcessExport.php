<?php

namespace App\Jobs;

use App\Models\Export;
use App\Services\ExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 2;

    public function __construct(public Export $export)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(ExportService $service)
    {
        try {
            Log::info("Starting export {$this->export->id}");
            
            $this->export->update(['status' => 'processing']);

            $meeting = $this->export->meeting;

            // Generate export based on format
            $path = match($this->export->format) {
                'pdf' => $service->exportToPdf($meeting),
                'docx' => $service->exportToWord($meeting),
                'txt' => $service->exportToText($meeting),
                default => throw new \Exception('Invalid export format'),
            };

            // Update export record
            $this->export->update([
                'file_path' => $path,
                'file_size' => Storage::size($path),
                'status' => 'completed',
            ]);

            Log::info("Export {$this->export->id} completed");

        } catch (\Exception $e) {
            Log::error("Export {$this->export->id} failed: " . $e->getMessage());
            
            $this->export->update(['status' => 'failed']);
            
            $this->fail($e);
        }
    }
}
