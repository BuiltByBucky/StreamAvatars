<?php

namespace App\Observers;

use App\Models\Avatar;
use App\Models\AvatarItem;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        $avatar = $user->avatar()->create(['last_active_at' => now()]);
        $user->progress()->create();

        // Assign all is_default items (one per slot type)
        $defaultItems = AvatarItem::where('is_default', true)->get()->keyBy('type');

        $slotUpdates = [];
        $unlocks = [];

        foreach (Avatar::$slots as $slot) {
            if ($item = $defaultItems->get($slot)) {
                $slotUpdates["{$slot}_item_id"] = $item->id;
                $unlocks[$item->id] = ['unlocked_at' => now(), 'source' => 'default'];
            }
        }

        if ($slotUpdates) {
            $avatar->update($slotUpdates);
        }
        if ($unlocks) {
            $user->unlockedItems()->syncWithoutDetaching($unlocks);
        }
    }
}
