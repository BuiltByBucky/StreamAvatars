<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvatarItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminItemController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(AvatarItem::orderBy('type')->orderBy('sort_order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        return response()->json(AvatarItem::create($data), 201);
    }

    public function update(Request $request, AvatarItem $item): JsonResponse
    {
        $data = $this->validated($request);
        $item->update($data);
        return response()->json($item);
    }

    public function destroy(AvatarItem $item): JsonResponse
    {
        $item->delete();
        return response()->json(null, 204);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:avatar_items,slug,' . ($request->route('item')?->id),
            'type' => 'required|in:base,skin,eyes,mouth,hair,shirt,pants,shoes,hat,glasses,accessory,back,pet,effect,badge',
            'rarity' => 'required|in:common,uncommon,rare,epic,legendary,mythic,event,subscriber,vip',
            'image_path' => 'required|string',
            'animated_image_path' => 'nullable|string',
            'price' => 'nullable|integer|min:0',
            'unlock_type' => 'nullable|string',
            'unlock_value' => 'nullable|string',
            'is_default' => 'boolean',
            'is_subscriber_only' => 'boolean',
            'is_vip_only' => 'boolean',
            'is_event_only' => 'boolean',
            'is_hidden' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);
    }
}
