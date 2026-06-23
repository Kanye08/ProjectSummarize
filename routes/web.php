<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\TranscriptionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/system/run-queue', function () {
    Artisan::call('queue:work', [
        '--once' => true,
        '--stop-when-empty' => true,
        '--queue' => 'default',
        '--no-interaction' => true,
        '--quiet' => true,   
    ]);
    return response()->json([
        'status' => 'success',
        'message' => 'Queue processed'
    ]);
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware(['auth','verified'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
        // dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    // Meetings
    Route::get('/meetings/record', [MeetingController::class, 'record'])->name('meetings.record');
    Route::post('/meetings/record/save', [MeetingController::class, 'saveRecording'])->name('meetings.record.save');
    Route::resource('meetings', MeetingController::class);
    
    Route::get('/meetings/{meeting}/audio', [MeetingController::class, 'audio'])->name('meetings.audio');

    // Transcripts
    Route::get('/meetings/{meeting}/transcript', [TranscriptionController::class, 'show'])->name('transcripts.show');
    Route::get('/meetings/{meeting}/transcript/search', [TranscriptionController::class, 'search'])->name('transcripts.search');
    Route::get('/meetings/{meeting}/transcript/download', [TranscriptionController::class, 'download'])->name('transcripts.download');

    // Exports
    Route::post('/meetings/{meeting}/export', [ExportController::class, 'export'])->name('meetings.export');
    Route::get('/exports/{export}/download', [ExportController::class, 'download'])->name('exports.download');
    Route::get('/exports', [ExportController::class, 'index'])->name('exports.index');

// Admin Routes
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::get('/activities', [AdminController::class, 'activities'])->name('activities');
        Route::get('/meetings/stats', [AdminController::class, 'meetingStats'])->name('meetings.stats');
    });

require __DIR__.'/auth.php';
