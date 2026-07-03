<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
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

        return $this->decodeResponse($response, 'Impossible de connecter Strava.');
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

        $payload = $this->decodeResponse($response, 'Impossible de rafraichir la session Strava.');

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
            $clubs = $this->decodeResponse(
                $this->request($accessToken)->get('/athlete/clubs', [
                    'page' => $page,
                    'per_page' => 200,
                ]),
                'Impossible de verifier le club Strava.'
            );

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
            $batch = $this->decodeResponse(
                $this->request($user->access_token)->get('/athlete/activities', [
                    'page' => $page,
                    'per_page' => 200,
                ]),
                'Impossible de synchroniser les activites Strava.'
            );

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

    private function decodeResponse(Response $response, string $fallbackMessage): array
    {
        try {
            return $response->throw()->json();
        } catch (RequestException $exception) {
            if ($this->isInactiveApplicationResponse($exception->response)) {
                throw new RuntimeException(
                    "L'application Strava configuree est inactive. Activez-la dans le tableau de bord Strava API, puis reessayez.",
                    previous: $exception
                );
            }

            throw new RuntimeException($fallbackMessage, previous: $exception);
        }
    }

    private function isInactiveApplicationResponse(?Response $response): bool
    {
        if (! $response || $response->status() !== 403) {
            return false;
        }

        return collect($response->json('errors', []))->contains(fn (array $error) => ($error['resource'] ?? null) === 'Application'
            && ($error['field'] ?? null) === 'Status'
            && ($error['code'] ?? null) === 'Inactive');
    }
}
