<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_pages_redirect_guests_to_login_page(): void
    {
        foreach (['/', '/groupe'] as $uri) {
            $this->get($uri)->assertRedirect(route('login'));
        }
    }

    public function test_authenticated_post_actions_redirect_guests_to_login_page(): void
    {
        $this->post('/sync')->assertRedirect(route('login'));
        $this->post('/logout')->assertRedirect(route('login'));
    }

    public function test_login_page_explains_strava_connection_is_required(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSee('Connexion requise')
            ->assertSee('Connect with Strava')
            ->assertSee(asset('images/logo-blanko.svg'), false)
            ->assertSee(asset('images/btn_strava_connect_with_orange.svg'), false)
            ->assertSee(route('strava.redirect'), false);

        $this->assertSame(2, substr_count($response->getContent(), asset('images/btn_strava_connect_with_orange.svg')));
    }

    public function test_group_page_defaults_to_current_week_period(): void
    {
        $member = User::query()->create([
            'strava_id' => 12345,
            'name' => 'Charles Robin',
            'access_token' => 'access-token',
            'refresh_token' => 'refresh-token',
        ]);

        $this->actingAs($member)
            ->get('/groupe')
            ->assertOk()
            ->assertSee('Total')
            ->assertSee('Activit&eacute;s', false)
            ->assertSee('<option value="week" selected>Cette semaine</option>', false);
    }

    public function test_group_page_displays_total_activities_for_selected_period(): void
    {
        $member = User::query()->create([
            'strava_id' => 12345,
            'name' => 'Charles Robin',
            'access_token' => 'access-token',
            'refresh_token' => 'refresh-token',
        ]);

        $inactiveMember = User::query()->create([
            'strava_id' => 67890,
            'name' => 'Membre Inactif',
            'access_token' => 'access-token',
            'refresh_token' => 'refresh-token',
        ]);

        Activity::query()->create([
            'user_id' => $member->id,
            'strava_id' => 111,
            'name' => 'Sortie une',
            'distance' => 5000,
            'moving_time' => 1800,
            'total_elevation_gain' => 125,
            'started_at' => now(),
        ]);

        Activity::query()->create([
            'user_id' => $inactiveMember->id,
            'strava_id' => 222,
            'name' => 'Sortie hors filtre',
            'moving_time' => 1800,
            'started_at' => now()->subWeek(),
        ]);

        $this->actingAs($member)
            ->get('/groupe?period=week')
            ->assertOk()
            ->assertSee('Activit&eacute;s', false)
            ->assertSee('1')
            ->assertSee('0 h 30')
            ->assertSee('2,0')
            ->assertSee('5,0 km')
            ->assertSee('125 m')
            ->assertSee('Charles Robin')
            ->assertDontSee('Membre Inactif');
    }
}
