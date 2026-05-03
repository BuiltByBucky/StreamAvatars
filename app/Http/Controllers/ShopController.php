<?php

namespace App\Http\Controllers;

use App\Models\AvatarItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(): JsonResponse
    {
        $items = AvatarItem::where('is_hidden', false)
            ->whereNotNull('price')
            ->orderBy('type')
            ->orderBy('sort_order')
            ->get();

        return response()->json($items);
    }

    public function buy(Request $request, AvatarItem $item): JsonResponse
    {
        $user = $request->user();
        $progress = $user->progress()->firstOrCreate(['user_id' => $user->id]);

        if ($progress->coins < $item->price) {
            return response()->json(['message' => 'Not enough coins.'], 422);
        }

        if ($user->unlockedItems()->where('avatar_item_id', $item->id)->exists()) {
            return response()->json(['message' => 'Already owned.'], 422);
        }

        $progress->decrement('coins', $item->price);
        $user->unlockedItems()->attach($item->id, [
            'unlocked_at' => now(),
            'source' => 'shop',
        ]);

        return response()->json(['message' => 'Purchased!', 'item' => $item]);
    }
}
