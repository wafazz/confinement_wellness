<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE commissions MODIFY COLUMN type ENUM('direct', 'override', 'affiliate') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE commissions MODIFY COLUMN type ENUM('direct', 'override') NOT NULL");
    }
};
