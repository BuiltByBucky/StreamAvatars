<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OverlaySetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOverlayController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $settings = $request->user()->overlaySettings()->firstOrCreate([
            'channel_id' => $request->user()->id,
        ]);

        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'avatar_scale' => 'numeric|min:0.5|max:3',
            'max_visible_avatars' => 'integer|min:1|max:100',
            'show_inactive_viewers' => 'boolean',
            'inactive_timeout_seconds' => 'integer|min:60',
            'position' => 'in:bottom,top',
            'animation_mode' => 'in:idle,walk,dance,minimal',
        ]);

        $settings = $request->user()->overlaySettings()->firstOrCreate([
            'channel_id' => $request->user()->id,
        ]);
        $settings->update($data);

        return response()->json($settings);
    }

    public function triggerAnimation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'animation' => 'required|string',
        ]);

        // Broadcast global animation event
        // broadcast(new GlobalAnimationTriggered($data['animation']))->toOthers();

        return response()->json(['triggered' => $data['animation']]);
    }
}
