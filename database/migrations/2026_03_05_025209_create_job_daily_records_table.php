<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_daily_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_job_id')->constrained('service_jobs')->cascadeOnDelete();
            $table->integer('day_number');
            $table->date('date');
            $table->enum('status', ['pending', 'checked_in', 'completed'])->default('pending');

            // Therapist check-in/out
            $table->dateTime('therapist_check_in_at')->nullable();
            $table->decimal('therapist_check_in_lat', 10, 7)->nullable();
            $table->decimal('therapist_check_in_lng', 10, 7)->nullable();
            $table->dateTime('therapist_check_out_at')->nullable();
            $table->decimal('therapist_check_out_lat', 10, 7)->nullable();
            $table->decimal('therapist_check_out_lng', 10, 7)->nullable();

            // Leader check-in/out
            $table->dateTime('leader_check_in_at')->nullable();
            $table->decimal('leader_check_in_lat', 10, 7)->nullable();
            $table->decimal('leader_check_in_lng', 10, 7)->nullable();
            $table->dateTime('leader_check_out_at')->nullable();
            $table->decimal('leader_check_out_lat', 10, 7)->nullable();
            $table->decimal('leader_check_out_lng', 10, 7)->nullable();

            $table->timestamps();

            $table->unique(['service_job_id', 'day_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_daily_records');
    }
};
