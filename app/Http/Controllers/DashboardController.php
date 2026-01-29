<?php

namespace App\Http\Controllers;

use App\Models\Export;
use App\Models\Meeting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Calculate stats
        $stats = [
            'total_meetings' => Meeting::where('user_id', $user->id)->count(),
            'total_transcriptions' => Meeting::where('user_id', $user->id)
                ->whereHas('transcript')
                ->count(),
            'storage_used' => $this->calculateStorageUsed($user->id),
            'export_credits' => 10, // Implement credits system later
            'audio_files' => Meeting::where('user_id', $user->id)
                ->whereNotNull('audio_file_path')
                ->count(),
            'video_files' => 0,
            'documents' => Export::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
        ];

        // Get recent meetings
        $recent_meetings = Meeting::where('user_id', $user->id)
            ->with(['transcript', 'summary'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_meetings'));
    }

    private function calculateStorageUsed($userId)
    {
        $totalBytes = Meeting::where('user_id', $userId)
            ->sum('audio_file_size');

        // Convert to GB
        $totalGB = $totalBytes / (1024 * 1024 * 1024);

        return round($totalGB, 2);
    }
}
