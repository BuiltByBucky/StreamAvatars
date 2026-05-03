<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('event_type');
            $table->enum('reward_type', ['xp', 'coins', 'item']);
            $table->string('reward_value');
            $table->string('required_value')->nullable();
            $table->unsignedInteger('cooldown_seconds')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_rules');
    }
};
