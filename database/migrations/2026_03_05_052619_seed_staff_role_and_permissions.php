<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'access-leaders',
            'access-therapists',
            'access-jobs',
            'access-bookings',
            'access-commissions',
            'access-points',
            'access-commission-rules',
            'access-reward-tiers',
            'access-sop-materials',
            'access-reviews',
            'access-staff',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $hq = Role::firstOrCreate(['name' => 'hq', 'guard_name' => 'web']);
        $hq->syncPermissions($permissions);

        Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
    }

    public function down(): void
    {
        Role::where('name', 'staff')->delete();
        Permission::whereIn('name', [
            'access-leaders', 'access-therapists', 'access-jobs', 'access-bookings',
            'access-commissions', 'access-points', 'access-commission-rules',
            'access-reward-tiers', 'access-sop-materials', 'access-reviews', 'access-staff',
        ])->delete();
    }
};
