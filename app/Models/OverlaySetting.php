<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OverlaySetting extends Model
{
    protected $fillable = [
        'channel_id',
        'overlay_token',
        'avatar_scale',
        'max_visible_avatars',
        'show_inactive_viewers',
        'inactive_timeout_seconds',
        'position',
        'animation_mode',
    ];

    protected $casts = [
        'avatar_scale' => 'float',
        'show_inactive_viewers' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->overlay_token)) {
                $model->overlay_token = Str::random(32);
            }
        });
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'channel_id');
    }
}
