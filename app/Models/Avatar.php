<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avatar extends Model
{
    protected $fillable = [
        'user_id',
        'base_item_id',
        'skin_item_id',
        'eyes_item_id',
        'mouth_item_id',
        'hair_item_id',
        'shirt_item_id',
        'pants_item_id',
        'shoes_item_id',
        'hat_item_id',
        'glasses_item_id',
        'accessory_item_id',
        'back_item_id',
        'pet_item_id',
        'effect_item_id',
        'badge_item_id',
        'is_visible',
        'last_active_at',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    public static array $slots = [
        'base', 'skin', 'eyes', 'mouth', 'hair',
        'shirt', 'pants', 'shoes', 'hat', 'glasses',
        'accessory', 'back', 'pet', 'effect', 'badge',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function base(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'base_item_id'); }
    public function skin(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'skin_item_id'); }
    public function eyes(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'eyes_item_id'); }
    public function mouth(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'mouth_item_id'); }
    public function hair(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'hair_item_id'); }
    public function shirt(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'shirt_item_id'); }
    public function pants(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'pants_item_id'); }
    public function shoes(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'shoes_item_id'); }
    public function hat(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'hat_item_id'); }
    public function glasses(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'glasses_item_id'); }
    public function accessory(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'accessory_item_id'); }
    public function back(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'back_item_id'); }
    public function pet(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'pet_item_id'); }
    public function effect(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'effect_item_id'); }
    public function badge(): BelongsTo { return $this->belongsTo(AvatarItem::class, 'badge_item_id'); }
}
