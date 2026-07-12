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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('theme')->default('light'); // light, dark, system
            $table->string('ai_language')->default('auto'); // auto, id, en
            $table->string('ai_tone')->default('friendly'); // professional, friendly, casual, academic
            $table->string('ai_response_length')->default('medium'); // short, medium, long
            $table->string('dashboard_layout')->default('grid'); // grid, list
            $table->integer('notes_per_page')->default(12);
            $table->string('default_sort')->default('latest'); // latest, oldest, title_asc, title_desc
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
