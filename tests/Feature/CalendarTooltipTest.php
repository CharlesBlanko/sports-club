<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTooltipTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_tooltip_displays_member_activity_time_for_sport(): void
    {
        $member = User::query()->create([
            'strava_id' => 12345,
            'name' => 'Charles Robin',
            'access_token' => 'access-token',
            'refresh_token' => 'refresh-token',
        ]);

        Activity::query()->create([
            'user_id' => $member->id,
            'strava_id' => 98765,
            'name' => 'Sortie du matin',
            'sport_type' => 'Ride',
            'type' => 'Ride',
            'moving_time' => 3780,
            'started_at' => '2026-07-04 10:00:00',
        ]);

        $this->actingAs($member);

        $this->get('/?calendar=2026-07')
            ->assertOk()
            ->assertSee('Charles Robin&nbsp;-&nbsp;1h03', false)
            ->assertSee('>4,2</span>', false)
            ->assertSee('energy-block-icon', false)
            ->assertSee("Blocs d'&eacute;nergie", false);
    }
}
