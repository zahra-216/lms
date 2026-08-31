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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('duration_minutes')->default(60); // Time limit in minutes
            $table->integer('total_points')->default(100);
            $table->integer('max_attempts')->default(1); // Number of attempts allowed
            $table->enum('grading_type', ['automatic', 'manual', 'both'])->default('manual'); // auto, manual, or both
            $table->boolean('show_correct_answers')->default(false); // Whether to show correct answers after submission
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
