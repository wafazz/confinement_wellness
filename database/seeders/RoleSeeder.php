<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
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
            'access-reports',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $hq = Role::firstOrCreate(['name' => 'hq']);
        $hq->syncPermissions($permissions);

        Role::firstOrCreate(['name' => 'leader']);
        Role::firstOrCreate(['name' => 'therapist']);
        Role::firstOrCreate(['name' => 'staff']);
    }
}
