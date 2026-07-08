<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_pages_redirect_guests_to_login_page(): void
    {
        foreach (['/', '/groupe', '/activites'] as $uri) {
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
}
