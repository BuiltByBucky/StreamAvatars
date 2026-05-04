<?php

namespace App\Http\Controllers;

use App\Events\AvatarUpdated;
use App\Models\Avatar;
use App\Models\BotEvent;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BotController extends Controller
{
    /**
     * Called whenever a viewer sends a chat message or is detected as active.
     * Auto-creates a user + default avatar if they don't exist yet.
     */
    public function viewerActive(Request $request): JsonResponse
    {
        $data = $request->validate([
            'twitch_id'            => 'required|string',
            'twitch_username'      => 'required|string',
            'twitch_display_name'  => 'nullable|string',
            'twitch_profile_image' => 'nullable|string',
        ]);

        $displayName = $data['twitch_display_name'] ?? $data['twitch_username'];

        $user = User::firstOrCreate(
            ['twitch_id' => $data['twitch_id']],
            [
                'name'                 => $displayName,
                'twitch_username'      => $data['twitch_username'],
                'twitch_display_name'  => $displayName,
                'twitch_profile_image' => $data['twitch_profile_image'] ?? null,
            ]
        );

        // Update profile image if bot now has it and we didn't before
        if (!empty($data['twitch_profile_image']) && !$user->twitch_profile_image) {
            $user->update(['twitch_profile_image' => $data['twitch_profile_image']]);
        }

        // Ensure avatar exists (observer handles new users, but safety net for edge cases)
        $avatar = $user->avatar()->firstOrCreate(['user_id' => $user->id], ['last_active_at' => now()]);

        $wasInactive = !$avatar->last_active_at
            || $avatar->last_active_at->lt(now()->subMinutes(6));

        $avatar->update(['last_active_at' => now()]);

        // Broadcast on first join or returning after inactivity
        // so the overlay picks them up without a page refresh
        if ($user->wasRecentlyCreated || $wasInactive) {
            $avatar->load([...Avatar::$slots, 'user:id,twitch_display_name,twitch_username,twitch_profile_image']);
            broadcast(new AvatarUpdated($avatar));
        } elseif ($avatar->last_active_at?->lt(now()->subSeconds(55))) {
            // Heartbeat every ~60s to keep overlay in sync
            $avatar->load([...Avatar::$slots, 'user:id,twitch_display_name,twitch_username,twitch_profile_image']);
            broadcast(new AvatarUpdated($avatar));
        }

        return response()->json(['ok' => true]);
    }

    public function chatMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'twitch_id'            => 'required|string',
            'twitch_username'      => 'required|string',
            'twitch_display_name'  => 'nullable|string',
            'twitch_profile_image' => 'nullable|string',
            'message'              => 'required|string',
        ]);

        // Treat chat as activity signal
        $this->viewerActive(new Request([
            'twitch_id'            => $data['twitch_id'],
            'twitch_username'      => $data['twitch_username'],
            'twitch_display_name'  => $data['twitch_display_name'] ?? null,
            'twitch_profile_image' => $data['twitch_profile_image'] ?? null,
        ]));

        BotEvent::create([
            'event_type' => 'chat_message',
            'payload'    => $data,
        ]);

        return response()->json(['ok' => true]);
    }

    public function command(Request $request): JsonResponse
    {
        $data = $request->validate([
            'twitch_id' => 'required|string',
            'command'   => 'required|string',
            'args'      => 'nullable|array',
        ]);

        BotEvent::create([
            'event_type' => 'command',
            'payload'    => $data,
        ]);

        return response()->json(['ok' => true]);
    }

    public function eventSub(Request $request): JsonResponse
    {
        // Twitch challenge handshake
        if ($request->header('Twitch-Eventsub-Message-Type') === 'webhook_callback_verification') {
            return response()->json($request->input('challenge'));
        }

        BotEvent::create([
            'event_type' => $request->input('subscription.type', 'unknown'),
            'payload'    => $request->all(),
        ]);

        return response()->json(['ok' => true]);
    }
}
