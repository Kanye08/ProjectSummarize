<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Export extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $appends = ['download_url'];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDownloadUrlAttribute()
    {
        if ($this->file_path && $this->status === 'completed') {
            if (config('filesystems.default') === 's3') {
                return Storage::disk('s3')->temporaryUrl(
                    $this->file_path,
                    now()->addHours(1)
                );
            }
            return Storage::url($this->file_path);
        }
        return null;
    }
}
