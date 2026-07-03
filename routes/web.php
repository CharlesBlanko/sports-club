<?php

use App\Http\Controllers\Auth\StravaAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SyncStravaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/auth/strava/redirect', [StravaAuthController::class, 'redirect'])->name('strava.redirect');
Route::get('/auth/strava/callback', [StravaAuthController::class, 'callback'])->name('strava.callback');
Route::post('/logout', [StravaAuthController::class, 'logout'])->name('logout');

Route::post('/sync', SyncStravaController::class)
    ->middleware('auth')
    ->name('sync');
