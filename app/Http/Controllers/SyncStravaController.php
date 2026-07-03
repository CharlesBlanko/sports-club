<?php

namespace App\Http\Controllers;

use App\Actions\SyncStravaActivities;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class SyncStravaController extends Controller
{
    public function __invoke(SyncStravaActivities $sync): RedirectResponse
    {
        try {
            $count = $sync->handle(Auth::user());

            return back()->with('status', $count.' activites synchronisees.');
        } catch (RuntimeException $exception) {
            return back()->withErrors(['strava' => $exception->getMessage()]);
        }
    }
}
