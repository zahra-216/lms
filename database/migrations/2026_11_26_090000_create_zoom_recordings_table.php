<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per video file of a Zoom class recording (a class paused and restarted has several parts).
        // waiting -> working -> done | failed. The files are moved to YouTube one at a time by `zoom:recordings`.
        Schema::create('zoom_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zoom_meeting_id')->constrained()->cascadeOnDelete();
            $table->string('file_id', 120)->unique();           // Zoom's id of this recording file
            $table->string('recording_type', 60)->nullable();    // e.g. shared_screen_with_speaker_view
            $table->unsignedBigInteger('file_size')->default(0);
            $table->dateTime('recorded_from')->nullable();
            $table->dateTime('recorded_to')->nullable();
            $table->unsignedSmallInteger('part')->default(1);
            $table->text('download_url')->nullable();
            $table->string('status', 20)->default('waiting');
            $table->string('step', 20)->nullable();              // downloading | uploading
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('next_try_at')->nullable();
            $table->text('session_uri')->nullable();             // YouTube upload session, so an upload can carry on
            $table->string('youtube_video_id', 40)->nullable();
            $table->boolean('lms_done')->default(false);         // Recording rows were made for the modules
            $table->text('playlists')->nullable();               // playlist names, for the class page
            $table->text('last_error')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'next_try_at']);
        });

        Schema::table('zoom_meetings', function (Blueprint $table) {
            // the safety net asks Zoom for the recording now and then until it appears
            $table->dateTime('recordings_checked_at')->nullable();
            $table->unsignedSmallInteger('recordings_checks')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->dropColumn(['recordings_checked_at', 'recordings_checks']);
        });
        Schema::dropIfExists('zoom_recordings');
    }
};
