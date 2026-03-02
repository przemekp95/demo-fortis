<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'campaigns.manage',
            'draws.manage',
            'fraud.review',
            'winners.export',
            'api.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $adminRole = Role::findOrCreate('admin');
        $adminRole->syncPermissions($permissions);

        $participantRole = Role::findOrCreate('participant');
        $participantRole->syncPermissions([]);
    }
}
