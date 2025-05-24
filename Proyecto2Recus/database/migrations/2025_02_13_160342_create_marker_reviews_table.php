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
        Schema::create('marker_reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('review_stars');
            $table->string('review_content')->nullable();
            $table->integer('user_id');
            $table->integer('marker_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marker_reviews');
    }
};
