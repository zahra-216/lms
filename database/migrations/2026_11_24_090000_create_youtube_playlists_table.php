<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Which YouTube playlist a programme's (course + level) class recordings go into.
        // Filled by the admin on the YouTube Playlists page, or by itself the first time a recording is uploaded.
        Schema::create('youtube_playlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('level_id')->constrained()->cascadeOnDelete();
            $table->string('playlist_id', 80);
            $table->string('title', 200);
            $table->timestamps();

            $table->unique(['course_id', 'level_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('youtube_playlists');
    }
};
