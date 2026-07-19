<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    protected $fillable = [
        'drama_id', 'title', 'episode_number', 'video_url',
        'duration', 'description', 'thumbnail_url',
    ];

    public function drama(): BelongsTo
    {
        return $this->belongsTo(Drama::class);
    }
}