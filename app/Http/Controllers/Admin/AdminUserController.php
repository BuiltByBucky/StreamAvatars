<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvatarItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(User::with('progress')->paginate(50));
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user->load('avatar', 'progress', 'unlockedItems'));
    }

    public function updateProgress(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'xp' => 'nullable|integer|min:0',
            'coins' => 'nullable|integer|min:0',
            'level' => 'nullable|integer|min:1',
        ]);

        $progress = $user->progress()->firstOrCreate(['user_id' => $user->id]);
        $progress->update(array_filter($data, fn ($v) => $v !== null));

        return response()->json($progress);
    }

    public function giveItem(Request $request, User $user): JsonResponse
    {
        $data = $request->validate(['item_id' => 'required|exists:avatar_items,id']);
        $item = AvatarItem::findOrFail($data['item_id']);

        $user->unlockedItems()->syncWithoutDetaching([
            $item->id => ['unlocked_at' => now(), 'source' => 'admin'],
        ]);

        return response()->json(['message' => 'Item granted.']);
    }

    public function removeItem(User $user, AvatarItem $item): JsonResponse
    {
        $user->unlockedItems()->detach($item->id);

        return response()->json(['message' => 'Item removed.']);
    }
}
