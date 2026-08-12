<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE lecturer_payments MODIFY COLUMN payment_type ENUM('per_month','per_hour','per_day') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE lecturer_payments MODIFY COLUMN payment_type ENUM('per_month','per_hour') NOT NULL");
    }
};