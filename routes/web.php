<?php

use App\Http\Controllers\Auth\TwitchController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/twitch/redirect', [TwitchController::class, 'redirect'])->name('auth.twitch.redirect');
Route::get('/auth/twitch/callback', [TwitchController::class, 'callback'])->name('auth.twitch.callback');
Route::post('/logout', [TwitchController::class, 'logout'])->middleware('auth')->name('logout');

// Overlay — transparent blade shell (no dark background)
Route::get('/overlay/{channel}/{token}', function () {
    return view('overlay');
});

// SPA catch-all — Vue Router handles everything else
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
