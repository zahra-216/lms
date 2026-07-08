<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->dateTime('due_date');
            $table->integer('total_points')->default(100);
            $table->boolean('allow_late')->default(false);
            $table->integer('late_penalty')->default(0);
            $table->enum('submission_type', ['file','text','both'])->default('both');
            $table->integer('max_file_size')->default(10); // MB
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index('subject_id');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};