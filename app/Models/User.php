<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'twitch_id',
        'twitch_username',
        'twitch_display_name',
        'twitch_profile_image',
        'twitch_token',
        'twitch_refresh_token',
        'twitch_token_expires_at',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'twitch_token',
        'twitch_refresh_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'twitch_token_expires_at' => 'datetime',
        ];
    }

    public function avatar(): HasOne
    {
        return $this->hasOne(Avatar::class);
    }

    public function progress(): HasOne
    {
        return $this->hasOne(UserProgress::class);
    }

    public function overlaySettings(): HasOne
    {
        return $this->hasOne(OverlaySetting::class, 'channel_id');
    }

    public function unlockedItems(): BelongsToMany
    {
        return $this->belongsToMany(AvatarItem::class, 'user_avatar_items')
            ->withPivot('unlocked_at', 'source')
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModerator(): bool
    {
        return in_array($this->role, ['moderator', 'admin']);
    }
}
