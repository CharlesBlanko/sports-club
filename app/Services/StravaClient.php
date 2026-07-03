<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class StravaClient
{
    private const BASE_URL = 'https://www.strava.com/api/v3';

    public function authorizationUrl(string $state): string
    {
        return 'https://www.strava.com/oauth/authorize?'.http_build_query([
            'client_id' => config('services.strava.client_id'),
            'redirect_uri' => config('services.strava.redirect_uri'),
            'response_type' => 'code',
            'approval_prompt' => 'auto',
            'scope' => 'read,activity:read_all',
            'state' => $state,
        ]);
    }

    public function exchangeCode(string $code): array
    {
        $response = Http::asForm()->post('https://www.strava.com/oauth/token', [
            'client_id' => config('services.strava.client_id'),
            'client_secret' => config('services.strava.client_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code',
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Impossible de connecter Strava.');
        }

        return $response->json();
    }

    public function refreshToken(User $user): User
    {
        if ($user->token_expires_at && $user->token_expires_at->isFuture()) {
            return $user;
        }

        $response = Http::asForm()->post('https://www.strava.com/oauth/token', [
            'client_id' => config('services.strava.client_id'),
            'client_secret' => config('services.strava.client_secret'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $user->refresh_token,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Impossible de rafraichir la session Strava.');
        }

        $payload = $response->json();

        $user->forceFill([
            'access_token' => $payload['access_token'],
            'refresh_token' => $payload['refresh_token'],
            'token_expires_at' => Carbon::createFromTimestamp($payload['expires_at']),
        ])->save();

        return $user->refresh();
    }

    public function athleteBelongsToConfiguredClub(string $accessToken): bool
    {
        $clubId = (int) config('services.strava.club_id');

        if (! $clubId) {
            throw new RuntimeException('STRAVA_CLUB_ID doit etre configure.');
        }

        $page = 1;

        do {
            $clubs = $this->request($accessToken)->get('/athlete/clubs', [
                'page' => $page,
                'per_page' => 200,
            ])->throw()->json();

            if (collect($clubs)->contains(fn (array $club) => (int) $club['id'] === $clubId)) {
                return true;
            }

            $page++;
        } while (count($clubs) === 200);

        return false;
    }

    public function activities(User $user): array
    {
        $user = $this->refreshToken($user);
        $activities = [];
        $page = 1;

        do {
            $batch = $this->request($user->access_token)->get('/athlete/activities', [
                'page' => $page,
                'per_page' => 200,
            ])->throw()->json();

            array_push($activities, ...$batch);
            $page++;
        } while (count($batch) === 200);

        return $activities;
    }

    private function request(string $accessToken): PendingRequest
    {
        return Http::baseUrl(self::BASE_URL)
            ->acceptJson()
            ->withToken($accessToken)
            ->timeout(30);
    }
}
