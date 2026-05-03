<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overlay_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('users')->cascadeOnDelete();
            $table->string('overlay_token')->unique();
            $table->float('avatar_scale')->default(1.0);
            $table->unsignedSmallInteger('max_visible_avatars')->default(20);
            $table->boolean('show_inactive_viewers')->default(false);
            $table->unsignedInteger('inactive_timeout_seconds')->default(300);
            $table->enum('position', ['bottom', 'top'])->default('bottom');
            $table->enum('animation_mode', ['idle', 'walk', 'dance', 'minimal'])->default('walk');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overlay_settings');
    }
};
