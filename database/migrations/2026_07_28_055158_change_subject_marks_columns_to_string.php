<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subject_marks', function (Blueprint $table) {
            $table->string('assignment_marks')->nullable()->change();
            $table->string('mid_marks')->nullable()->change();
            $table->string('practical_marks')->nullable()->change();
            $table->string('final_exam_marks')->nullable()->change();
            $table->string('final_marks')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('subject_marks', function (Blueprint $table) {
            $table->integer('assignment_marks')->nullable()->change();
            $table->integer('mid_marks')->nullable()->change();
            $table->integer('practical_marks')->nullable()->change();
            $table->integer('final_exam_marks')->nullable()->change();
            $table->integer('final_marks')->nullable()->change();
        });
    }
};
