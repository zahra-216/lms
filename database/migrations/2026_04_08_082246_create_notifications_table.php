<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Laravel polymorphic relation
            $table->string('type');
            $table->morphs('notifiable'); 
            // creates:
            // notifiable_id
            // notifiable_type

            $table->text('data'); // JSON data (title, message, link etc)
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
        });
    }
    

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};