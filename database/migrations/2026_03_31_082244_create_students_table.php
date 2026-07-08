<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            $table->string('registration_no')->unique();
            $table->string('name');
            $table->string('email')->nullable();

            $table->string('branch');

            // login password
            $table->string('password')->nullable();

            // 🔥 ADD THIS FOR LMS SYSTEM
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();

            // 🔥 ONLINE USERS FEATURE (MOST IMPORTANT)
            $table->timestamp('last_seen_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};