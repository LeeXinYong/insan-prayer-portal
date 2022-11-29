<?php

namespace Database\Seeders;

use App\Enums\DefaultRole;
use App\Models\ProtectedRole;
use App\Models\UnlimitedAccessRole;
use App\Models\UnlimitedAccessRolePermission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = $this->data();

        foreach ($data as $value) {

            $shouldUpdatePermissions = false;

            try{
                /** @var Role $role */
                $role = Role::findByName($value['name']);

            } catch (RoleDoesNotExist $e) {
                $role = new Role();
                $role->name = $value['name'];
                $shouldUpdatePermissions = true;
            }

            if ($value['color']) {
                $role->color = $value['color'];
            }

            if ($value['defaultRole']) {
                $role->default_role = $value['defaultRole'];
            }

            $role->save();

            if ($shouldUpdatePermissions) {
                $permissions = $value['defaultPermissions'] ?? [];
                $role->syncPermissions($permissions);
            }

            if ($value['protected'] ?? false) {
                ProtectedRole::query()->updateOrCreate([
                    'role_id' => $role->id,
                ]);
            }

            if ($value['unlimitedAccess'] ?? false) {
                $unlimitedAccessRole = UnlimitedAccessRole::query()->updateOrCreate([
                    'role_id' => $role->id,
                ]);

                foreach (Permission::all() as $permission) {
                    UnlimitedAccessRolePermission::query()->updateOrCreate([
                        'role_id' => $role->id,
                        'permission_id' => $permission->id,
                        'unlimited_access_role_id' => $unlimitedAccessRole->id,
                    ]);
                }
            }
        }
    }

    public function data()
    {
        return array_map(function($role) {
            return [
                'name' => $role->value,
                'defaultPermissions' => $role->getDefaultPermissions(),
                'protected' => $role->isProtected(),
                'unlimitedAccess' => $role->hasUnlimitedAccess(),
                'color' => $role->getColor(),
                'defaultRole' => $role->value,
            ];
        }, DefaultRole::cases());
//        return [
//            ['name' => 'Super Admin', 'defaultPermissions' => Permission::all(), 'protected' => true, 'unlimitedAccess' => true],
//            ['name' => 'Admin', 'defaultPermissions' => Permission::all(), 'protected' => true],
//            ['name' => 'User'],
//        ];
    }
}
