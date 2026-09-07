<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gate_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_type'); // 'student' or 'lecturer'
            $table->unsignedBigInteger('user_id');
            $table->enum('scan_type', ['in', 'out']);
            $table->timestamp('scanned_at');
            $table->string('device_id')->nullable(); // which gate device sent this
            $table->timestamps();

            $table->index(['user_type', 'user_id', 'scanned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_logs');
    }
};