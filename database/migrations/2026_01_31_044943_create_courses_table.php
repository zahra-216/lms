<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
    Schema::create('courses', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('faculty_id');
     $table->string('code')->unique(); // Course code
    $table->string('name');       // Course name
    $table->text('description')->nullable();
    $table->enum('status', ['active','inactive','archived'])->default('active');
    $table->timestamps();

    $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
});


    }

    public function down() {
        Schema::dropIfExists('courses');
    }
};
