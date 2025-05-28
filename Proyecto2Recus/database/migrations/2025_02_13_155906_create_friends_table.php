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
        Schema::create('friends', function (Blueprint $table) {
            $table->id();

            $table->boolean('request_status')->default(false);

            $table->foreignId('sender_user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('reciver_user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('friend_group_id')
                ->nullable()
                ->constrained('friend_groups')
                ->nullOnDelete();

            $table->timestamps();

            // Asegura que no se repitan relaciones
            $table->unique(['sender_user_id', 'reciver_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friends');
    }
};
