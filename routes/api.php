<?php

use App\Http\Controllers\Admin\AdminItemController;
use App\Http\Controllers\Admin\AdminOverlayController;
use App\Http\Controllers\Admin\AdminRewardRuleController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\OverlayController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Current user
Route::middleware('auth:sanctum')->get('/me', [UserController::class, 'me']);

// Avatar
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/avatar', [AvatarController::class, 'show']);
    Route::put('/avatar', [AvatarController::class, 'update']);
    Route::post('/avatar/randomize', [AvatarController::class, 'randomize']);
    Route::post('/avatar/reset', [AvatarController::class, 'reset']);
    Route::get('/avatar/items', [AvatarController::class, 'items']);
    Route::get('/avatar/inventory', [AvatarController::class, 'inventory']);
    Route::get('/avatar/progress', [AvatarController::class, 'progress']);
});

// Shop
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/shop/items', [ShopController::class, 'index']);
    Route::post('/shop/items/{item}/buy', [ShopController::class, 'buy']);
});

// Overlay (token-based, no login)
Route::get('/overlay/{channel}/{token}/state', [OverlayController::class, 'state']);

// Bot webhooks (secured via BotSecretMiddleware)
Route::middleware('bot.secret')->group(function () {
    Route::post('/bot/chat-message', [BotController::class, 'chatMessage']);
    Route::post('/bot/viewer-active', [BotController::class, 'viewerActive']);
    Route::post('/bot/command', [BotController::class, 'command']);
});

// Twitch EventSub
Route::post('/twitch/eventsub', [BotController::class, 'eventSub']);

// Admin
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{user}', [AdminUserController::class, 'show']);
    Route::put('/users/{user}/progress', [AdminUserController::class, 'updateProgress']);
    Route::post('/users/{user}/items', [AdminUserController::class, 'giveItem']);
    Route::delete('/users/{user}/items/{item}', [AdminUserController::class, 'removeItem']);

    Route::apiResource('items', AdminItemController::class);
    Route::apiResource('reward-rules', AdminRewardRuleController::class);

    Route::get('/overlay-settings', [AdminOverlayController::class, 'show']);
    Route::put('/overlay-settings', [AdminOverlayController::class, 'update']);
    Route::post('/overlay/events/global-animation', [AdminOverlayController::class, 'triggerAnimation']);
});
