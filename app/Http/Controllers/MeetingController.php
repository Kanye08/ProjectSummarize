<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessAudioTranscription;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MeetingController extends Controller
{
    public function index()
    {
        $meetings = Meeting::where('user_id', auth()->id())
            ->with(['transcript', 'summary', 'sentimentAnalysis'])
            ->latest()
            ->paginate(15);

        return view('meetings.index', compact('meetings'));
    }

    public function create()
    {
        return view('meetings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'nullable|date',
            'audio' => 'required|file|mimes:mp3,wav,m4a,ogg,webm,mpeg|max:524288', // 512MB
        ]);

        $audioFile = $request->file('audio');
        
        // Store file
        $path = $audioFile->store('meetings/audio', 'public');

        // Get audio duration using getID3 or similar (optional)
        $duration = null;
        
        // Create meeting record
        $meeting = Meeting::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'start_time' => $request->start_time ?? now(),
            'location' => 'Online',
            'status' => 'completed',
            'audio_file_path' => $path,
            'audio_file_name' => $audioFile->getClientOriginalName(),
            'audio_file_size' => $audioFile->getSize(),
            'audio_format' => $audioFile->getClientOriginalExtension(),
            'duration' => $duration,
            'processing_status' => 'uploaded',
        ]);

        // Log activity
        auth()->user()->activities()->create([
            'action' => 'create_meeting',
            'description' => "Created meeting: {$meeting->title}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Dispatch job to process transcription
        ProcessAudioTranscription::dispatch($meeting);

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Meeting uploaded successfully! Transcription is being processed.');
    }

    public function show(Meeting $meeting)
    {
        // Check if user owns this meeting
        if ($meeting->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $meeting->load(['transcript', 'summary', 'sentimentAnalysis', 'exports']);

        return view('meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting)
    {
        if ($meeting->user_id !== auth()->id()) {
            abort(403);
        }

        return view('meetings.edit', compact('meeting'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        if ($meeting->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'nullable|date',
            'location' => 'nullable|string|max:255',
        ]);

        $meeting->update($request->only(['title', 'description', 'start_time', 'location']));

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Meeting updated successfully.');
    }

    public function destroy(Meeting $meeting)
    {
        if ($meeting->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete audio file
        if ($meeting->audio_file_path) {
            Storage::disk('public')->delete($meeting->audio_file_path);
        }

        $meeting->delete();

        return redirect()->route('meetings.index')
            ->with('success', 'Meeting deleted successfully.');
    }
}
