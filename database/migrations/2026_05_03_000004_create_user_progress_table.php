<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('xp')->default(0);
            $table->unsignedSmallInteger('level')->default(1);
            $table->unsignedBigInteger('coins')->default(0);
            $table->unsignedInteger('watchtime_minutes')->default(0);
            $table->unsignedInteger('chat_messages_count')->default(0);
            $table->timestamp('last_rewarded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
