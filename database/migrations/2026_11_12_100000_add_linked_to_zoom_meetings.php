<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // true = the meeting was made in the Zoom app and only linked to modules in the LMS.
        // The LMS never changes or cancels such a meeting in Zoom.
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->boolean('linked')->default(false)->after('passcode');
        });
    }

    public function down(): void
    {
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->dropColumn('linked');
        });
    }
};
