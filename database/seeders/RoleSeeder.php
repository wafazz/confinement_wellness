<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'hq']);
        Role::create(['name' => 'leader']);
        Role::create(['name' => 'therapist']);
    }
}
