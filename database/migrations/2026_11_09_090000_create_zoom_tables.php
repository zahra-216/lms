<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per Zoom class created from the LMS.
        Schema::create('zoom_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('zoom_meeting_id')->unique();     // the number Zoom gives the meeting
            $table->string('zoom_uuid')->nullable();         // set when Zoom tells us the meeting ended
            $table->string('topic');
            $table->dateTime('start_time');                   // scheduled start, campus time
            $table->unsignedSmallInteger('duration_minutes');
            $table->string('join_url', 500);
            $table->string('passcode')->nullable();
            // scheduled -> ended (Zoom said it finished) -> processed | failed
            $table->string('status', 20)->default('scheduled');
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->dateTime('ended_at')->nullable();
            $table->dateTime('next_try_at')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->text('last_error')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();   // admin id
            $table->timestamps();

            $table->index(['status', 'next_try_at']);
        });

        // The modules (subjects) a class covers. Different modules can share one class.
        Schema::create('zoom_meeting_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zoom_meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->unique(['zoom_meeting_id', 'subject_id']);
        });

        // What Zoom said about who joined (one row per join).
        Schema::create('zoom_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zoom_meeting_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->dateTime('join_time')->nullable();
            $table->dateTime('leave_time')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('matched_by', 10)->nullable();   // auto | manual
            $table->timestamps();

            $table->index(['zoom_meeting_id', 'student_id']);
        });

        // Tells apart marks made by hand from marks that came from Zoom.
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('source', 10)->default('manual')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('source');
        });
        Schema::dropIfExists('zoom_participants');
        Schema::dropIfExists('zoom_meeting_subject');
        Schema::dropIfExists('zoom_meetings');
    }
};
