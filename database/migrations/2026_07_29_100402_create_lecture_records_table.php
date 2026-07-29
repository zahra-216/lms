<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lecture_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecturer_id')->nullable()->constrained('lecturers')->onDelete('set null');

            $table->text('content_covered')->nullable();
            $table->date('date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->enum('created_by', ['admin', 'lecturer'])->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecture_records');
    }
};
