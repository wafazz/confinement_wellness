<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email')->nullable();
            $table->text('client_address');
            $table->string('state');
            $table->string('district');
            $table->string('service_type');
            $table->date('preferred_date');
            $table->time('preferred_time');
            $table->foreignId('preferred_therapist_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending_review', 'approved', 'rejected', 'converted'])->default('pending_review');
            $table->enum('source', ['guest', 'registered'])->default('guest');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
