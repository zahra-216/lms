<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('type', ['document', 'video', 'link', 'text' , 'image'])->default('document');
            $table->string('file_path', 255)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_type', 50)->nullable();
            $table->longText('content')->nullable();
            $table->string('url', 500)->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('subject_id');
            $table->index('type');
            $table->index('order');
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};