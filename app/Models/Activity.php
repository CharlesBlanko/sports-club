<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'strava_id',
        'name',
        'sport_type',
        'type',
        'distance',
        'moving_time',
        'elapsed_time',
        'total_elevation_gain',
        'started_at',
        'timezone',
        'commute',
        'trainer',
        'manual',
        'raw',
    ];

    protected function casts(): array
    {
        return [
            'distance' => 'float',
            'total_elevation_gain' => 'float',
            'started_at' => 'datetime',
            'commute' => 'boolean',
            'trainer' => 'boolean',
            'manual' => 'boolean',
            'raw' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
