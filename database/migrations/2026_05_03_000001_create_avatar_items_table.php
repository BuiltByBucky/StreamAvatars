<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avatar_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['base', 'skin', 'eyes', 'mouth', 'hair', 'shirt', 'pants', 'shoes', 'hat', 'glasses', 'accessory', 'back', 'pet', 'effect', 'badge']);
            $table->enum('rarity', ['common', 'uncommon', 'rare', 'epic', 'legendary', 'mythic', 'event', 'subscriber', 'vip'])->default('common');
            $table->string('image_path');
            $table->string('animated_image_path')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->string('unlock_type')->nullable();
            $table->string('unlock_value')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_subscriber_only')->default(false);
            $table->boolean('is_vip_only')->default(false);
            $table->boolean('is_event_only')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avatar_items');
    }
};
