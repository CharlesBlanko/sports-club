<?php

namespace Tests\Feature;

use App\Actions\SyncStravaActivities;
use App\Models\Activity;
use App\Models\User;
use App\Services\StravaClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SyncStravaActivitiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_the_activity_start_in_its_local_timezone(): void
    {
        $user = User::query()->create([
            'strava_id' => 12345,
            'name' => 'Charles Robin',
            'access_token' => 'access-token',
            'refresh_token' => 'refresh-token',
        ]);

        $strava = Mockery::mock(StravaClient::class);
        $strava->shouldReceive('activities')->once()->with($user)->andReturn([[
            'id' => 98765,
            'name' => 'Entrainement du soir',
            'sport_type' => 'Workout',
            'start_date' => '2026-07-13T01:29:00Z',
            'start_date_local' => '2026-07-12T21:29:00Z',
            'timezone' => '(GMT-05:00) America/New_York',
        ]]);

        (new SyncStravaActivities($strava))->handle($user);

        $activity = Activity::query()->where('strava_id', 98765)->firstOrFail();

        $this->assertSame('2026-07-12 21:29:00', $activity->getRawOriginal('started_at'));
    }

    public function test_it_falls_back_to_the_application_timezone(): void
    {
        $user = User::query()->create([
            'strava_id' => 12345,
            'name' => 'Charles Robin',
            'access_token' => 'access-token',
            'refresh_token' => 'refresh-token',
        ]);

        $strava = Mockery::mock(StravaClient::class);
        $strava->shouldReceive('activities')->once()->andReturn([[
            'id' => 98765,
            'name' => 'Entrainement du soir',
            'sport_type' => 'Workout',
            'start_date' => '2026-07-13T01:29:00Z',
        ]]);

        (new SyncStravaActivities($strava))->handle($user);

        $activity = Activity::query()->where('strava_id', 98765)->firstOrFail();

        $this->assertSame('2026-07-12 21:29:00', $activity->getRawOriginal('started_at'));
    }
}
