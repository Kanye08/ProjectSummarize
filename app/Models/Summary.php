<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'action_points' => 'array',
        'key_decisions' => 'array',
        'key_topics' => 'array',
        'participants_mentioned' => 'array',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}
