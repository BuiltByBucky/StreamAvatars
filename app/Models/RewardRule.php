<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardRule extends Model
{
    protected $fillable = [
        'name',
        'event_type',
        'reward_type',
        'reward_value',
        'required_value',
        'cooldown_seconds',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cooldown_seconds' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEvent($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }
}
