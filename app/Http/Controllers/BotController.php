<?php

namespace App\Http\Controllers;

use App\Models\BotEvent;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BotController extends Controller
{
    public function chatMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'twitch_id' => 'required|string',
            'username' => 'required|string',
            'message' => 'required|string',
        ]);

        BotEvent::create([
            'event_type' => 'chat_message',
            'payload' => $data,
        ]);

        return response()->json(['ok' => true]);
    }

    public function viewerActive(Request $request): JsonResponse
    {
        $data = $request->validate([
            'twitch_id' => 'required|string',
        ]);

        $user = User::where('twitch_id', $data['twitch_id'])->first();

        if ($user?->avatar) {
            $user->avatar->touch('last_active_at');
        }

        return response()->json(['ok' => true]);
    }

    public function command(Request $request): JsonResponse
    {
        $data = $request->validate([
            'twitch_id' => 'required|string',
            'command' => 'required|string',
            'args' => 'nullable|array',
        ]);

        BotEvent::create([
            'event_type' => 'command',
            'payload' => $data,
        ]);

        return response()->json(['ok' => true]);
    }

    public function eventSub(Request $request): JsonResponse
    {
        // Twitch EventSub challenge verification
        if ($request->header('Twitch-Eventsub-Message-Type') === 'webhook_callback_verification') {
            return response()->json($request->input('challenge'));
        }

        BotEvent::create([
            'event_type' => $request->input('subscription.type', 'unknown'),
            'payload' => $request->all(),
        ]);

        return response()->json(['ok' => true]);
    }
}
