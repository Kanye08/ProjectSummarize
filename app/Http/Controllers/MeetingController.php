<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessAudioTranscription;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

    public function record()
    {
        return view('meetings.record');
    }

    public function saveRecording(Request $request)
    {
        try {
            $request->validate([
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string',
                'audio'       => 'required|file|mimes:mp3,wav,m4a,ogg,webm,mpeg|max:524288',
            ]);

            $audioFile = $request->file('audio');

            // Store the recorded audio
            $path = $audioFile->store('meetings/audio', 'public');

            // Create meeting record
            $meeting = Meeting::create([
                'user_id'           => auth()->id(),
                'title'             => $request->title,
                'description'       => $request->description ?? null,
                'start_time'        => $request->start_time ?? now(),
                'location'          => 'In-App Recording',
                'status'            => 'completed',
                'audio_file_path'   => $path,
                'audio_file_name'   => $audioFile->getClientOriginalName(),
                'audio_file_size'   => $audioFile->getSize(),
                'audio_format'      => $audioFile->getClientOriginalExtension(),
                'duration'          => $request->duration ?? null,
                'processing_status' => 'uploaded',
            ]);

            // Log activity
            auth()->user()->activities()->create([
                'action'      => 'record_meeting',
                'description' => "Recorded meeting: {$meeting->title}",
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            // Dispatch transcription job
            ProcessAudioTranscription::dispatch($meeting);

            Log::info("Recording saved and job dispatched for meeting {$meeting->id}");

            // Always return JSON since record page uses AJAX
            return response()->json([
                'success'    => true,
                'meeting_id' => $meeting->id,
                'message'    => 'Recording saved! Transcription is being processed.',
                'redirect'   => route('meetings.show', $meeting),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            Log::error("Save recording failed: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save recording: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─── Upload form store (existing) ───────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time'  => 'nullable|date',
            'audio'       => 'required|file|mimes:mp3,wav,m4a,ogg,webm,mpeg|max:524288',
        ]);

        $audioFile = $request->file('audio');

        $path = $audioFile->store('meetings/audio', 'public');

        $meeting = Meeting::create([
            'user_id'           => auth()->id(),
            'title'             => $request->title,
            'description'       => $request->description,
            'start_time'        => $request->start_time ?? now(),
            'location'          => 'Online',
            'status'            => 'completed',
            'audio_file_path'   => $path,
            'audio_file_name'   => $audioFile->getClientOriginalName(),
            'audio_file_size'   => $audioFile->getSize(),
            'audio_format'      => $audioFile->getClientOriginalExtension(),
            'duration'          => null,
            'processing_status' => 'uploaded',
        ]);

        auth()->user()->activities()->create([
            'action'      => 'create_meeting',
            'description' => "Created meeting: {$meeting->title}",
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        ProcessAudioTranscription::dispatch($meeting);

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Meeting uploaded successfully! Transcription is being processed.');
    }

    public function show(Meeting $meeting)
    {
        if ($meeting->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $meeting->load(['transcript', 'summary', 'sentimentAnalysis', 'exports']);

        return view('meetings.show', compact('meeting'));
    }

    public function status(Meeting $meeting)
    {
        if ($meeting->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json([
            'processing_status' => $meeting->processing_status,
            'error_message'     => $meeting->error_message,
        ]);
    }

    public function audio(Meeting $meeting)
    {
        if ($meeting->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!$meeting->audio_file_path || !Storage::disk('public')->exists($meeting->audio_file_path)) {
            abort(404, 'Audio file not found');
        }

        $path = Storage::disk('public')->path($meeting->audio_file_path);

        return response()->file($path, [
            'Content-Type' => Storage::disk('public')->mimeType($meeting->audio_file_path) ?: 'audio/webm',
            'Accept-Ranges' => 'bytes',
        ]);
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
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time'  => 'nullable|date',
            'location'    => 'nullable|string|max:255',
        ]);

        $meeting->update($request->only([
            'title',
            'description',
            'start_time',
            'location',
        ]));

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Meeting updated successfully.');
    }

    public function destroy(Meeting $meeting)
    {
        if ($meeting->user_id !== auth()->id()) {
            abort(403);
        }

        if ($meeting->audio_file_path) {
            Storage::disk('public')->delete($meeting->audio_file_path);
        }

        $meeting->delete();

        return redirect()->route('meetings.index')
            ->with('success', 'Meeting deleted successfully.');
    }
}