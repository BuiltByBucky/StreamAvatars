<?php

namespace App\Http\Controllers;

use App\Events\AvatarUpdated;
use App\Models\Avatar;
use App\Models\AvatarItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $avatar = $request->user()
            ->avatar()
            ->with(Avatar::$slots)
            ->firstOrCreate(['user_id' => $request->user()->id]);

        return response()->json($avatar);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate(
            collect(Avatar::$slots)
                ->mapWithKeys(fn ($slot) => ["{$slot}_item_id" => 'nullable|exists:avatar_items,id'])
                ->toArray()
        );

        $avatar = $request->user()->avatar()->firstOrCreate(['user_id' => $request->user()->id]);
        $avatar->fill($validated)->fill(['last_active_at' => now()])->save();
        $avatar->load([...Avatar::$slots, 'user:id,twitch_display_name,twitch_username,twitch_profile_image']);

        broadcast(new AvatarUpdated($avatar));

        return response()->json($avatar);
    }

    public function items(Request $request): JsonResponse
    {
        $items = AvatarItem::where('is_hidden', false)
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json($items);
    }

    public function inventory(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->unlockedItems()->get()
        );
    }

    public function progress(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->progress()->firstOrCreate(['user_id' => $request->user()->id])
        );
    }

    public function randomize(Request $request): JsonResponse
    {
        $user = $request->user();
        $avatar = $user->avatar()->firstOrCreate(['user_id' => $user->id]);

        $unlockedIds = $user->unlockedItems()->pluck('avatar_items.id');

        $equipped = [];
        foreach (Avatar::$slots as $slot) {
            $item = AvatarItem::where('type', $slot)
                ->where('is_hidden', false)
                ->where(function ($q) use ($unlockedIds) {
                    $q->where('is_default', true)->orWhereIn('id', $unlockedIds);
                })
                ->inRandomOrder()
                ->first();

            $equipped["{$slot}_item_id"] = $item?->id;
        }

        $avatar->fill($equipped)->fill(['last_active_at' => now()])->save();
        $avatar->load([...Avatar::$slots, 'user:id,twitch_display_name,twitch_username,twitch_profile_image']);

        broadcast(new AvatarUpdated($avatar));

        return response()->json($avatar);
    }

    public function reset(Request $request): JsonResponse
    {
        $avatar = $request->user()->avatar()->firstOrCreate(['user_id' => $request->user()->id]);

        $nulled = collect(Avatar::$slots)->mapWithKeys(fn ($s) => ["{$s}_item_id" => null])->toArray();
        $avatar->fill($nulled)->fill(['last_active_at' => now()])->save();

        $defaults = [];
        foreach (Avatar::$slots as $slot) {
            $item = AvatarItem::where('type', $slot)->where('is_default', true)->first();
            if ($item) {
                $defaults["{$slot}_item_id"] = $item->id;
            }
        }

        if ($defaults) {
            $avatar->fill($defaults)->save();
        }

        $avatar->load([...Avatar::$slots, 'user:id,twitch_display_name,twitch_username,twitch_profile_image']);
        broadcast(new AvatarUpdated($avatar));

        return response()->json($avatar);
    }
}
