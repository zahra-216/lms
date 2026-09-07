<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique()->after('registration_no');
        });

        Schema::table('lecturers', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique()->after('username');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });

        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });
    }
};