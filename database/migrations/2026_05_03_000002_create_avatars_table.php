<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avatars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('base_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('skin_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('eyes_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('mouth_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('hair_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('shirt_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('pants_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('shoes_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('hat_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('glasses_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('accessory_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('back_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('pet_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('effect_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->foreignId('badge_item_id')->nullable()->constrained('avatar_items')->nullOnDelete();
            $table->boolean('is_visible')->default(true);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avatars');
    }
};
