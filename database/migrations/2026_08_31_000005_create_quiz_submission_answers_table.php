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
        Schema::create('quiz_submission_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_submission_id');
            $table->unsignedBigInteger('quiz_question_id');
            $table->unsignedBigInteger('quiz_answer_id')->nullable(); // For multiple choice and true/false
            $table->text('answer_text')->nullable(); // For short answer questions
            $table->boolean('is_correct')->nullable(); // Whether answer is correct (null = not graded yet)
            $table->timestamps();

            $table->foreign('quiz_submission_id')->references('id')->on('quiz_submissions')->onDelete('cascade');
            $table->foreign('quiz_question_id')->references('id')->on('quiz_questions')->onDelete('cascade');
            $table->foreign('quiz_answer_id')->references('id')->on('quiz_answers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_submission_answers');
    }
};
