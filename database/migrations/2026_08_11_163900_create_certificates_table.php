<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->string('student_name');
            $table->string('father_name');
            $table->date('date_of_birth');
            $table->string('course');
            $table->date('course_start');
            $table->date('course_end');
            $table->enum('award_status', ['Distinction', 'Merit', 'Pass']);
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};