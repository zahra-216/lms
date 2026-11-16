<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * The old timetable (one entry per subject) is removed completely, with its data.
 * New design: one timetable for each course + level + semester, made of rows (day, time, module, lecturer).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('timetables');

        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('level_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->string('batch', 60)->nullable();      // optional, e.g. "Batch 25"
            $table->string('mode_note', 150)->nullable(); // optional, e.g. "Online via Zoom - 1-hour sessions"
            $table->timestamps();

            $table->unique(['course_id', 'level_id', 'semester_id']);
        });

        Schema::create('timetable_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_id')->constrained()->cascadeOnDelete();
            $table->string('day', 12);
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lecturer_id')->constrained()->cascadeOnDelete();
            $table->string('note', 150)->nullable();      // optional, e.g. "Combined class with HND BM"
            $table->timestamps();

            $table->index('lecturer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_rows');
        Schema::dropIfExists('timetables');
    }
};
