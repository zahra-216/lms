<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gate_logs', function (Blueprint $table) {
            $table->dropColumn('device_id');
            $table->unsignedBigInteger('gate_device_id')->nullable()->after('scan_type');
        });
    }

    public function down(): void
    {
        Schema::table('gate_logs', function (Blueprint $table) {
            $table->dropColumn('gate_device_id');
            $table->string('device_id')->nullable();
        });
    }
};