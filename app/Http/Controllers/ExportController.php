<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessExport;
use App\Models\Export;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    public function index()
    {
        $exports = Export::where('user_id', auth()->id())
            ->with('meeting')
            ->latest()
            ->paginate(20);

        return view('exports.index', compact('exports'));
    }

    public function export(Request $request, Meeting $meeting)
    {
        if ($meeting->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'format' => 'required|in:pdf,docx,txt',
        ]);

        // Check if meeting is processed
        if ($meeting->processing_status !== 'completed') {
            return back()->with('error', 'Meeting is still being processed. Please wait.');
        }

        // Create export record
        $export = Export::create([
            'meeting_id' => $meeting->id,
            'user_id' => auth()->id(),
            'format' => $request->format,
            'file_path' => '',
            'status' => 'pending',
        ]);

        // Dispatch job
        ProcessExport::dispatch($export);

        return back()->with('success', 'Export is being generated. You will be notified when ready.');
    }

    public function download(Export $export)
    {
        if ($export->user_id !== auth()->id()) {
            abort(403);
        }

        if ($export->status !== 'completed') {
            return back()->with('error', 'Export is not ready yet.');
        }

        return Storage::download($export->file_path);
    }
}
