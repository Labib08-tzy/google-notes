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
        // 1. Update notes table to support favorites, pinning, and soft deletion
        Schema::table('notes', function (Blueprint $table) {
            $table->boolean('is_favorite')->default(false)->after('content');
            $table->boolean('is_pinned')->default(false)->after('is_favorite');
            $table->timestamp('archived_at')->nullable()->after('is_archived');
            $table->softDeletes()->after('updated_at');
        });

        // 2. Create tags table
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            
            // Prevent duplicate tag names per user
            $table->unique(['user_id', 'name']);
        });

        // 3. Create note_tag pivot table
        Schema::create('note_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            
            $table->unique(['note_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_tag');
        Schema::dropIfExists('tags');
        
        Schema::table('notes', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['is_favorite', 'is_pinned', 'archived_at']);
        });
    }
};
