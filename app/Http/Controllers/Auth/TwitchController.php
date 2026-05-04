<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class TwitchController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('twitch')->stateless()->redirect();
    }

    public function callback(): RedirectResponse
    {
        $twitchUser = Socialite::driver('twitch')->stateless()->user();

        // Merge into existing bot-created account if present, otherwise create fresh
        $user = User::updateOrCreate(
            ['twitch_id' => $twitchUser->getId()],
            [
                'name'                    => $twitchUser->getName(),
                'email'                   => $twitchUser->getEmail(),
                'twitch_username'         => $twitchUser->getNickname(),
                'twitch_display_name'     => $twitchUser->getName(),
                'twitch_profile_image'    => $twitchUser->getAvatar(),
                'twitch_token'            => $twitchUser->token,
                'twitch_refresh_token'    => $twitchUser->refreshToken,
                'twitch_token_expires_at' => now()->addSeconds($twitchUser->expiresIn ?? 3600),
            ]
        );

        Auth::login($user, remember: true);

        return redirect('/avatar');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
