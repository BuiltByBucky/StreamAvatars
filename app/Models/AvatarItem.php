<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AvatarItem extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'rarity',
        'image_path',
        'animated_image_path',
        'price',
        'unlock_type',
        'unlock_value',
        'is_default',
        'is_subscriber_only',
        'is_vip_only',
        'is_event_only',
        'is_hidden',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_default' => 'boolean',
        'is_subscriber_only' => 'boolean',
        'is_vip_only' => 'boolean',
        'is_event_only' => 'boolean',
        'is_hidden' => 'boolean',
    ];

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_avatar_items')
            ->withPivot('unlocked_at', 'source')
            ->withTimestamps();
    }
}
