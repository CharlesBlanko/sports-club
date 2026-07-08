<?php

namespace Tests\Feature;

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
        $this->get('/login')
            ->assertOk()
            ->assertSee('Connexion requise')
            ->assertSee('Connexion Strava')
            ->assertSee(route('strava.redirect'), false);
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
            ->assertSee('<option value="week" selected>Cette semaine</option>', false);
    }
}
