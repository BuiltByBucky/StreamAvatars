<?php

namespace App\Http\Controllers;

use App\Models\Avatar;
use App\Models\OverlaySetting;
use Illuminate\Http\JsonResponse;

class OverlayController extends Controller
{
    public function state(string $channel, string $token): JsonResponse
    {
        $settings = OverlaySetting::where('overlay_token', $token)
            ->whereHas('channel', fn ($q) => $q->whereRaw('LOWER(twitch_username) = ?', [strtolower($channel)]))
            ->firstOrFail();

        $avatars = Avatar::where('is_visible', true)
            ->whereHas('user')
            ->with(['user:id,twitch_display_name,twitch_profile_image', ...Avatar::$slots])
            ->when(!$settings->show_inactive_viewers, function ($q) use ($settings) {
                $q->where('last_active_at', '>=', now()->subSeconds($settings->inactive_timeout_seconds));
            })
            ->limit($settings->max_visible_avatars)
            ->get();

        return response()->json($avatars);
    }
}
