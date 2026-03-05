<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commission_rules', function (Blueprint $table) {
            $table->enum('therapist_commission_type', ['fixed', 'percentage'])->default('fixed')->after('therapist_commission');
            $table->enum('leader_override_type', ['fixed', 'percentage'])->default('fixed')->after('leader_override');
            $table->decimal('affiliate_commission', 10, 2)->default(0)->after('leader_override_type');
            $table->enum('affiliate_commission_type', ['fixed', 'percentage'])->default('fixed')->after('affiliate_commission');
            $table->integer('customer_referral_points')->default(0)->after('affiliate_commission_type');
        });
    }

    public function down(): void
    {
        Schema::table('commission_rules', function (Blueprint $table) {
            $table->dropColumn([
                'therapist_commission_type', 'leader_override_type',
                'affiliate_commission', 'affiliate_commission_type',
                'customer_referral_points',
            ]);
        });
    }
};
