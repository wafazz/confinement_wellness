<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_code')->unique();
            $table->string('client_name');
            $table->string('client_phone');
            $table->text('client_address');
            $table->string('state');
            $table->string('district');
            $table->string('service_type');
            $table->date('job_date');
            $table->time('job_time');
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'checked_in', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->decimal('checked_in_lat', 10, 7)->nullable();
            $table->decimal('checked_in_lng', 10, 7)->nullable();
            $table->dateTime('checked_out_at')->nullable();
            $table->decimal('checked_out_lat', 10, 7)->nullable();
            $table->decimal('checked_out_lng', 10, 7)->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_jobs');
    }
};
