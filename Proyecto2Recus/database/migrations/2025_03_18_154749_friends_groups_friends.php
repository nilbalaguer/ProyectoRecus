<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('friend_groups_friends', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('friend_group_id')
                ->constrained('friend_groups')
                ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['user_id', 'friend_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friend_groups_friends');
    }
};
