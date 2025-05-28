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
        Schema::create('marker_list_markers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('marker_list_id')
                ->constrained('marker_list')
                ->onDelete('cascade');

            $table->foreignId('marker_id')
                ->constrained('markers')
                ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['marker_list_id', 'marker_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marker_list_markers');
    }
};
