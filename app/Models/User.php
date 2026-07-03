<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'strava_id',
        'name',
        'firstname',
        'lastname',
        'profile',
        'city',
        'state',
        'country',
        'sex',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'last_synced_at',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    protected function casts(): array
    {
        return [
            'token_expires_at' => 'datetime',
            'last_synced_at' => 'datetime',
        ];
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
