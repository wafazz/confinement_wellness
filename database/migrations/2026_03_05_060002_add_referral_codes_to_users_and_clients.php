<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 20)->unique()->nullable()->after('status');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->string('referral_code', 20)->unique()->nullable()->after('status');
            $table->integer('reward_points')->default(0)->after('referral_code');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('referral_code', 20)->nullable()->after('notes');
            $table->string('referred_by_type', 20)->nullable()->after('referral_code');
            $table->unsignedBigInteger('referred_by_id')->nullable()->after('referred_by_type');
        });

        // Generate referral codes for existing users (leaders & therapists)
        $users = DB::table('users')->whereIn('role', ['leader', 'therapist'])->get();
        foreach ($users as $user) {
            DB::table('users')->where('id', $user->id)->update([
                'referral_code' => 'REF-' . strtoupper(Str::random(5)),
            ]);
        }

        // Generate referral codes for existing clients
        $clients = DB::table('clients')->get();
        foreach ($clients as $client) {
            DB::table('clients')->where('id', $client->id)->update([
                'referral_code' => 'CREF-' . strtoupper(Str::random(5)),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['referral_code', 'referred_by_type', 'referred_by_id']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['referral_code', 'reward_points']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referral_code');
        });
    }
};
