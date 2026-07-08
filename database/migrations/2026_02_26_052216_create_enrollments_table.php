<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('enrollments', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('student_id');
    $table->unsignedBigInteger('course_id');

    $table->enum('status', ['enrolled', 'completed', 'dropped'])->default('enrolled');
    $table->timestamp('enrolled_at')->nullable();
    $table->timestamps();

    // ❌ TEMP REMOVE foreign key
    // $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
});
    }

    public function down(): void {
        Schema::dropIfExists('enrollments');
    }
};