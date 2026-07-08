<?php

namespace App\Http\Controllers\Auth;

use App\Actions\SyncStravaActivities;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\StravaClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RuntimeException;

class StravaAuthController extends Controller
{
    public function redirect(Request $request, StravaClient $strava): RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put('strava_oauth_state', $state);

        return redirect()->away($strava->authorizationUrl($state));
    }

    public function callback(Request $request, StravaClient $strava, SyncStravaActivities $sync): RedirectResponse
    {
        abort_unless($request->string('state')->toString() === $request->session()->pull('strava_oauth_state'), 403);

        try {
            $payload = $strava->exchangeCode($request->string('code')->toString());

            if (! $strava->athleteBelongsToConfiguredClub($payload['access_token'])) {
                return redirect()->route('login')->withErrors([
                    'strava' => "Votre compte Strava n'est pas membre du club configure.",
                ]);
            }

            $athlete = $payload['athlete'];
            $name = trim(($athlete['firstname'] ?? '').' '.($athlete['lastname'] ?? '')) ?: ($athlete['username'] ?? 'Athlete Strava');

            $user = User::query()->updateOrCreate(
                ['strava_id' => $athlete['id']],
                [
                    'name' => $name,
                    'firstname' => $athlete['firstname'] ?? null,
                    'lastname' => $athlete['lastname'] ?? null,
                    'profile' => $athlete['profile'] ?? null,
                    'city' => $athlete['city'] ?? null,
                    'state' => $athlete['state'] ?? null,
                    'country' => $athlete['country'] ?? null,
                    'sex' => $athlete['sex'] ?? null,
                    'access_token' => $payload['access_token'],
                    'refresh_token' => $payload['refresh_token'],
                    'token_expires_at' => Carbon::createFromTimestamp($payload['expires_at']),
                ]
            );

            Auth::login($user, remember: true);
            $count = $sync->handle($user);

            return redirect()->route('dashboard')->with('status', $count.' activites synchronisees.');
        } catch (RuntimeException $exception) {
            return redirect()->route('login')->withErrors(['strava' => $exception->getMessage()]);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
