<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commission_rules', function (Blueprint $table) {
            $table->enum('service_category', ['stay_in', 'daily_visit', 'wellness'])->default('wellness')->after('id');
            $table->integer('work_days')->nullable()->after('service_category');
        });
    }

    public function down(): void
    {
        Schema::table('commission_rules', function (Blueprint $table) {
            $table->dropColumn(['service_category', 'work_days']);
        });
    }
};
