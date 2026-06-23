<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Meeting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    protected $appends = [
        'audio_url',
        'formatted_duration',
        'formatted_size',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transcript()
    {
        return $this->hasOne(Transcript::class);
    }

    public function summary()
    {
        return $this->hasOne(Summary::class);
    }

    public function sentimentAnalysis()
    {
        return $this->hasOne(Sentiment::class);
    }

    public function exports()
    {
        return $this->hasMany(Export::class);
    }

    // Accessors
    public function getAudioUrlAttribute()
    {
        if ($this->audio_file_path) {
            // For S3
            if (config('filesystems.default') === 's3') {
                return Storage::disk('s3')->temporaryUrl(
                    $this->audio_file_path,
                    now()->addHours(1)
                );
            }
            // For local storage — served via a route so it works even
            // if the public/storage symlink was never created on the host.
            return route('meetings.audio', $this);
        }
        return null;
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) return '0:00';
        
        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getFormattedSizeAttribute()
    {
        if (!$this->audio_file_size) return '0 MB';
        
        $size = $this->audio_file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('processing_status', 'completed');
    }

    public function scopeProcessing($query)
    {
        return $query->whereIn('processing_status', ['processing', 'transcribing', 'summarizing']);
    }

    public function scopeRecent($query, $limit = 5)
    {
        return $query->latest()->limit($limit);
    }
}
