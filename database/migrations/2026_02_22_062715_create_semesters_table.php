<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('level_id');   // Link to Levels
            $table->unsignedBigInteger('course_id');  // Link to Courses
            $table->string('name');                   // Semester Name
            $table->timestamps();

            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};