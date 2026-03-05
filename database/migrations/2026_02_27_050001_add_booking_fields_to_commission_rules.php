<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commission_rules', function (Blueprint $table) {
            $table->text('description')->nullable()->after('service_type');
            $table->decimal('price', 10, 2)->nullable()->after('description');
            $table->boolean('requires_review')->default(true)->after('points_per_job');
        });
    }

    public function down(): void
    {
        Schema::table('commission_rules', function (Blueprint $table) {
            $table->dropColumn(['description', 'price', 'requires_review']);
        });
    }
};
