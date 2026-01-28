<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transcript extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'segments' => 'array',
        'speakers' => 'array',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function searchText($query)
    {
        $segments = collect($this->segments)->filter(function ($segment) use ($query) {
            return stripos($segment['text'], $query) !== false;
        });

        return $segments->map(function ($segment) use ($query) {
            return [
                'segment' => $segment,
                'highlighted' => $this->highlightText($segment['text'], $query),
            ];
        });
    }

    private function highlightText($text, $query)
    {
        return preg_replace(
            '/(' . preg_quote($query, '/') . ')/i',
            '<mark class="bg-yellow-300 dark:bg-yellow-600">$1</mark>',
            $text
        );
    }
}
