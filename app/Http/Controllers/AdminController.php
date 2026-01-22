<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Meeting;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display admin dashboard
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_meetings' => Meeting::count(),
            'active_users' => User::whereHas('activities', function($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })->count(),
            'recent_activities' => UserActivity::latest()->take(10)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Display all users
     */
    public function users()
    {
        $users = User::withCount('meetings')
            ->withCount('activities')
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user edit form
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'is_admin' => ['boolean'],
        ]);

        $user->update($validated);

        // Log activity
        UserActivity::create([
            'user_id' => auth()->id(),
            'action' => 'update_user',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Updated user: {$user->name}",
        ]);

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        // Log activity
        UserActivity::create([
            'user_id' => auth()->id(),
            'action' => 'delete_user',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => "Deleted user: {$userName}",
        ]);

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    /**
     * Display user activities
     */
    public function activities()
    {
        $activities = UserActivity::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.activities.index', compact('activities'));
    }

    /**
     * Display meeting statistics
     */
    public function meetingStats()
    {
        $stats = [
            'total_meetings' => Meeting::count(),
            'scheduled_meetings' => Meeting::where('status', 'scheduled')->count(),
            'completed_meetings' => Meeting::where('status', 'completed')->count(),
            'cancelled_meetings' => Meeting::where('status', 'cancelled')->count(),
            'meetings_by_user' => Meeting::selectRaw('user_id, count(*) as count')
                ->groupBy('user_id')
                ->with('user')
                ->get(),
            'recent_meetings' => Meeting::with('user')->latest()->take(10)->get(),
        ];

        return view('admin.meetings.stats', compact('stats'));
    }
}
