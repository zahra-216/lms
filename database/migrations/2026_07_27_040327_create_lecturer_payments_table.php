<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lecturer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->onDelete('cascade');
            $table->enum('type_of_lecture', ['online', 'physical']);
            $table->date('date');
            $table->decimal('total_hours', 6, 2)->nullable();
            $table->enum('payment_type', ['per_month', 'per_hour']);
            $table->decimal('rate_amount', 10, 2);
            $table->decimal('total_payment', 10, 2);
            $table->decimal('completed_payment', 10, 2)->default(0);
            $table->date('paid_date')->nullable();
            $table->string('invoice_no')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturer_payments');
    }
};
