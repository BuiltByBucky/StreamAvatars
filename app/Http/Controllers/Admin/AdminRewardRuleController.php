<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminRewardRuleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(RewardRule::all());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        return response()->json(RewardRule::create($data), 201);
    }

    public function update(Request $request, RewardRule $rewardRule): JsonResponse
    {
        $rewardRule->update($this->validated($request));
        return response()->json($rewardRule);
    }

    public function destroy(RewardRule $rewardRule): JsonResponse
    {
        $rewardRule->delete();
        return response()->json(null, 204);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'event_type' => 'required|string',
            'reward_type' => 'required|in:xp,coins,item',
            'reward_value' => 'required|string',
            'required_value' => 'nullable|string',
            'cooldown_seconds' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
    }
}
