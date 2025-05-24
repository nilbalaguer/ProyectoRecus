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
            $table->foreignId('id_friend')->constrained('users');
            $table->foreignId('friend_group_id')->constrained('friend_groups');
            $table->timestamps();
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
