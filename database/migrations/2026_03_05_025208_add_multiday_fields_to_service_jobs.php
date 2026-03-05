<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_jobs', function (Blueprint $table) {
            $table->enum('service_category', ['stay_in', 'daily_visit', 'wellness'])->default('wellness')->after('service_type');
            $table->integer('work_days')->nullable()->after('service_category');
            $table->date('job_end_date')->nullable()->after('job_date');
            $table->integer('current_day')->default(0)->after('work_days');
        });
    }

    public function down(): void
    {
        Schema::table('service_jobs', function (Blueprint $table) {
            $table->dropColumn(['service_category', 'work_days', 'job_end_date', 'current_day']);
        });
    }
};
