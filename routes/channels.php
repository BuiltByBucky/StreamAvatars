<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Public overlay channel — token security handled at API level
Broadcast::channel('overlay.{channel}', fn () => true);
