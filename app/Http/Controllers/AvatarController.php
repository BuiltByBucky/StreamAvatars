<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $avatar = $request->user()->avatar()->with(array_map(
            fn ($slot) => $slot,
            \App\Models\Avatar::$slots
        ))->firstOrCreate(['user_id' => $request->user()->id]);

        return response()->json($avatar);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate(collect(\App\Models\Avatar::$slots)
            ->mapWithKeys(fn ($slot) => ["{$slot}_item_id" => 'nullable|exists:avatar_items,id'])
            ->toArray());

        $avatar = $request->user()->avatar()->firstOrCreate(['user_id' => $request->user()->id]);
        $avatar->update($validated);
        $avatar->touch('last_active_at');

        return response()->json($avatar);
    }

    public function items(Request $request): JsonResponse
    {
        $items = \App\Models\AvatarItem::where('is_hidden', false)
            ->orderBy('type')
            ->orderBy('sort_order')
            ->get();

        return response()->json($items);
    }

    public function inventory(Request $request): JsonResponse
    {
        return response()->json($request->user()->unlockedItems()->get());
    }

    public function progress(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->progress()->firstOrCreate(['user_id' => $request->user()->id])
        );
    }

    public function randomize(Request $request): JsonResponse
    {
        // Placeholder — full implementation when items exist
        return response()->json($request->user()->avatar);
    }

    public function reset(Request $request): JsonResponse
    {
        $avatar = $request->user()->avatar()->firstOrCreate(['user_id' => $request->user()->id]);
        $avatar->update(array_fill_keys(
            array_map(fn ($s) => "{$s}_item_id", \App\Models\Avatar::$slots),
            null
        ));

        return response()->json($avatar);
    }
}
