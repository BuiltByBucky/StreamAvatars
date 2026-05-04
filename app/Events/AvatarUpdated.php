<?php

namespace App\Events;

use App\Models\Avatar;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AvatarUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public Avatar $avatar) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('overlay.' . strtolower($this->avatar->user->twitch_username)),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AvatarUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'avatar' => $this->avatar
                ->loadMissing([...Avatar::$slots, 'user:id,twitch_display_name,twitch_username,twitch_profile_image'])
                ->toArray(),
        ];
    }
}
