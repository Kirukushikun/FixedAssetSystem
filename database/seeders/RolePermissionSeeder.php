<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Support\AccessControl;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (AccessControl::permissions() as $module => $permissions) {
            foreach ($permissions as $key => $name) {
                Permission::updateOrCreate(
                    ['key' => $key],
                    [
                        'module' => $module,
                        'name' => $name,
                    ]
                );
            }
        }

        foreach (AccessControl::roles() as $key => $roleDefinition) {
            $role = Role::updateOrCreate(
                ['key' => $key],
                [
                    'name' => $roleDefinition['name'],
                    'description' => $roleDefinition['description'],
                ]
            );

            $permissionIds = Permission::whereIn('key', $roleDefinition['permissions'])->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }
}
