<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TranscriptionController extends Controller
{
    public function show(Meeting $meeting)
    {
        if ($meeting->user_id !== auth()->id()) {
            abort(403);
        }

        $meeting->load('transcript');

        if (!$meeting->transcript) {
            abort(404, 'Transcript not found');
        }

        return view('transcripts.show', compact('meeting'));
    }

    public function search(Request $request, Meeting $meeting)
    {
        if ($meeting->user_id !== auth()->id()) {
            abort(403);
        }

        $query = $request->input('q');
        $transcript = $meeting->transcript;

        if (!$transcript) {
            return response()->json(['results' => []]);
        }

        $results = $transcript->searchText($query);

        return response()->json(['results' => $results]);
    }

    public function download(Meeting $meeting)
    {
        if ($meeting->user_id !== auth()->id()) {
            abort(403);
        }

        $transcript = $meeting->transcript;

        if (!$transcript) {
            abort(404);
        }

        $content = "Meeting: {$meeting->title}\n";
        $content .= "Date: " . $meeting->start_time->format('F j, Y') . "\n";
        $content .= str_repeat('=', 50) . "\n\n";
        $content .= $transcript->full_text;

        $filename = Str::slug($meeting->title) . '-transcript.txt';

        return response()->streamDownload(function() use ($content) {
            echo $content;
        }, $filename, ['Content-Type' => 'text/plain']);
    }
}
