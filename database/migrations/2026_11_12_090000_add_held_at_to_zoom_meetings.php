<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // When the class really started (from Zoom). Attendance is saved under this day, so a class that was
        // moved inside Zoom still lands on the right date.
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->dateTime('held_at')->nullable()->after('attempts');
        });
    }

    public function down(): void
    {
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->dropColumn('held_at');
        });
    }
};
