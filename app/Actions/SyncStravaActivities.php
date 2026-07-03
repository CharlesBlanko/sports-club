<?php

namespace App\Actions;

use App\Models\Activity;
use App\Models\User;
use App\Services\StravaClient;
use Illuminate\Support\Carbon;

class SyncStravaActivities
{
    public function __construct(private readonly StravaClient $strava)
    {
    }

    public function handle(User $user): int
    {
        $synced = 0;

        foreach ($this->strava->activities($user) as $activity) {
            Activity::query()->updateOrCreate(
                ['strava_id' => $activity['id']],
                [
                    'user_id' => $user->id,
                    'name' => $activity['name'] ?? 'Activite sans titre',
                    'sport_type' => $activity['sport_type'] ?? $activity['type'] ?? null,
                    'type' => $activity['type'] ?? null,
                    'distance' => $activity['distance'] ?? 0,
                    'moving_time' => $activity['moving_time'] ?? 0,
                    'elapsed_time' => $activity['elapsed_time'] ?? 0,
                    'total_elevation_gain' => $activity['total_elevation_gain'] ?? 0,
                    'started_at' => Carbon::parse($activity['start_date'] ?? $activity['start_date_local']),
                    'timezone' => $activity['timezone'] ?? null,
                    'commute' => $activity['commute'] ?? false,
                    'trainer' => $activity['trainer'] ?? false,
                    'manual' => $activity['manual'] ?? false,
                    'raw' => $activity,
                ]
            );

            $synced++;
        }

        $user->forceFill(['last_synced_at' => now()])->save();

        return $synced;
    }
}
