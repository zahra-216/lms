<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subject_marks', function (Blueprint $table) {
            $table->integer('practical_marks')->nullable()->after('assignment_marks');
        });
    }

    public function down(): void
    {
        Schema::table('subject_marks', function (Blueprint $table) {
            $table->dropColumn('practical_marks');
        });
    }
};