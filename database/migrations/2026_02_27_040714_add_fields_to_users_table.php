<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('ic_number')->nullable()->after('phone');
            $table->enum('role', ['hq', 'staff', 'leader', 'therapist'])->default('therapist')->after('password');
            $table->foreignId('leader_id')->nullable()->constrained('users')->nullOnDelete()->after('role');
            $table->string('state')->nullable()->after('leader_id');
            $table->string('district')->nullable()->after('state');
            $table->string('kkm_cert_no')->nullable()->after('district');
            $table->string('bank_name')->nullable()->after('kkm_cert_no');
            $table->string('bank_account')->nullable()->after('bank_name');
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active')->after('bank_account');
            $table->string('profile_photo')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->dropColumn([
                'phone', 'ic_number', 'role', 'leader_id', 'state', 'district',
                'kkm_cert_no', 'bank_name', 'bank_account', 'status', 'profile_photo',
            ]);
        });
    }
};
