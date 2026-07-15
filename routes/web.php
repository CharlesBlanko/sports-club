<?php

use App\Http\Controllers\Auth\StravaAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SyncStravaController;
use Illuminate\Support\Facades\Route;

Route::get('/login', fn () => view('auth.login'))->name('login');

Route::middleware('auth')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/groupe', [DashboardController::class, 'group'])->name('dashboard.group');
    Route::get('/activites', [DashboardController::class, 'activities'])->name('dashboard.activities');
    Route::post('/logout', [StravaAuthController::class, 'logout'])->name('logout');
    Route::post('/sync', SyncStravaController::class)->name('sync');
});

Route::get('/auth/strava/redirect', [StravaAuthController::class, 'redirect'])->name('strava.redirect');
Route::get('/auth/strava/callback', [StravaAuthController::class, 'callback'])->name('strava.callback');
