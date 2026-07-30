<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lecture_records', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
        });

        Schema::table('lecture_records', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->change();
        });

        Schema::table('lecture_records', function (Blueprint $table) {
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('lecture_records', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
        });

        Schema::table('lecture_records', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable(false)->change();
        });

        Schema::table('lecture_records', function (Blueprint $table) {
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }
};