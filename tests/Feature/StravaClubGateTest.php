<?php

namespace Tests\Feature;

use App\Services\StravaClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StravaClubGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_athlete_must_belong_to_configured_club(): void
    {
        config(['services.strava.club_id' => 123]);

        Http::fake([
            'www.strava.com/api/v3/athlete/clubs*' => Http::response([
                ['id' => 999, 'name' => 'Autre club'],
            ]),
        ]);

        $this->assertFalse(app(StravaClient::class)->athleteBelongsToConfiguredClub('token'));
    }

    public function test_inactive_strava_application_returns_clear_message(): void
    {
        config(['services.strava.club_id' => 123]);

        Http::fake([
            'www.strava.com/api/v3/athlete/clubs*' => Http::response([
                'message' => 'Forbidden',
                'errors' => [
                    [
                        'resource' => 'Application',
                        'field' => 'Status',
                        'code' => 'Inactive',
                    ],
                ],
            ], 403),
        ]);

        $this->expectExceptionMessage("L'application Strava configuree est inactive.");

        app(StravaClient::class)->athleteBelongsToConfiguredClub('token');
    }
}
