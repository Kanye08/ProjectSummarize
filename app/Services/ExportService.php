<?php

namespace App\Services;

use App\Models\Meeting;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    public function exportToPdf(Meeting $meeting)
    {
        $meeting->load(['transcript', 'summary', 'sentimentAnalysis']);

        $pdf = PDF::loadView('exports.meeting-pdf', [
            'meeting' => $meeting,
        ]);

        $filename = "meeting-{$meeting->id}-" . now()->format('Y-m-d') . ".pdf";
        $path = "exports/{$filename}";

        Storage::put($path, $pdf->output());

        return $path;
    }

    public function exportToWord(Meeting $meeting)
    {
        $meeting->load(['transcript', 'summary', 'sentimentAnalysis']);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Title
        $section->addTitle($meeting->title, 1);
        $section->addText('Date: ' . $meeting->start_time->format('F j, Y'));
        $section->addTextBreak(1);

        // Summary
        if ($meeting->summary) {
            $section->addTitle('Summary', 2);
            $section->addText($meeting->summary->summary_text);
            $section->addTextBreak(1);

            // Action Points
            if ($meeting->summary->action_points) {
                $section->addTitle('Action Points', 2);
                foreach ($meeting->summary->action_points as $point) {
                    $section->addListItem($point);
                }
                $section->addTextBreak(1);
            }

            // Key Decisions
            if ($meeting->summary->key_decisions) {
                $section->addTitle('Key Decisions', 2);
                foreach ($meeting->summary->key_decisions as $decision) {
                    $section->addListItem($decision);
                }
                $section->addTextBreak(1);
            }
        }

        // Transcript
        if ($meeting->transcript) {
            $section->addTitle('Full Transcript', 2);
            $section->addText($meeting->transcript->full_text);
        }

        $filename = "meeting-{$meeting->id}-" . now()->format('Y-m-d') . ".docx";
        $path = storage_path("app/exports/{$filename}");

        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($path);

        // Move to storage
        $storagePath = "exports/{$filename}";
        Storage::put($storagePath, file_get_contents($path));
        unlink($path);

        return $storagePath;
    }

    public function exportToText(Meeting $meeting)
    {
        $meeting->load(['transcript', 'summary']);

        $content = "Meeting: {$meeting->title}\n";
        $content .= "Date: " . $meeting->start_time->format('F j, Y') . "\n";
        $content .= str_repeat('=', 50) . "\n\n";

        if ($meeting->summary) {
            $content .= "SUMMARY\n";
            $content .= str_repeat('-', 50) . "\n";
            $content .= $meeting->summary->summary_text . "\n\n";

            if ($meeting->summary->action_points) {
                $content .= "ACTION POINTS\n";
                $content .= str_repeat('-', 50) . "\n";
                foreach ($meeting->summary->action_points as $point) {
                    $content .= "• {$point}\n";
                }
                $content .= "\n";
            }
        }

        if ($meeting->transcript) {
            $content .= "FULL TRANSCRIPT\n";
            $content .= str_repeat('-', 50) . "\n";
            $content .= $meeting->transcript->full_text . "\n";
        }

        $filename = "meeting-{$meeting->id}-" . now()->format('Y-m-d') . ".txt";
        $path = "exports/{$filename}";

        Storage::put($path, $content);

        return $path;
    }
}