<?php

namespace App\Actions;

use App\Models\Activity;
use App\Models\User;
use App\Services\StravaClient;
use Illuminate\Support\Carbon;

class SyncStravaActivities
{
    public function __construct(private readonly StravaClient $strava) {}

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
                    'started_at' => $this->startedAt($activity),
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

    private function startedAt(array $activity): Carbon
    {
        $timezone = $this->activityTimezone($activity['timezone'] ?? null);

        if (! empty($activity['start_date'])) {
            return Carbon::parse($activity['start_date'])->setTimezone($timezone);
        }

        // Strava suffixes start_date_local with "Z", although its clock value is
        // already local. Remove that suffix so Carbon does not interpret it as UTC.
        $localStart = preg_replace('/Z$/', '', $activity['start_date_local']);

        return Carbon::parse($localStart, $timezone);
    }

    private function activityTimezone(?string $timezone): string
    {
        $candidate = trim((string) preg_replace('/^\(GMT[^)]*\)\s*/', '', $timezone ?? ''));

        return in_array($candidate, timezone_identifiers_list(), true)
            ? $candidate
            : config('app.timezone');
    }
}
