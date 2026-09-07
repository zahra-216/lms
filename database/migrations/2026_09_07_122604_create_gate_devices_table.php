<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gate_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Main Gate Tablet"
            $table->string('device_key', 64)->unique(); // the secret key
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_devices');
    }
};