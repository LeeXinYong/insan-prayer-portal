<?php

namespace App\Providers;

use App\Enums\DefaultRole;
use App\Exceptions\EnumCaseNotFoundException;
use App\Exceptions\MustHaveAtLeastOneUserException;
use App\Exceptions\RoleModifiers\MustHaveAtLeastOneRoleException;
use App\Models\ModelHasRole;
use App\Models\UnlimitedAccessRole;
use App\Models\UnlimitedAccessRolePermission;
use App\Models\User;
use App\Models\UserRoleModifiers\CanHaveOnlyOneRole;
use App\Models\UserRoleModifiers\MustHasAtLeastOneRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class RoleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register the role modifiers
        // if user can only have one role
        ModelHasRole::creating(function (ModelHasRole $modelHasRole) {
            $model = $modelHasRole->pivotParent;
            /** @var User $model */
            if ($model->roles()->count() === 1 && $model instanceof CanHaveOnlyOneRole) {
                $model->roles()->get()->each(fn($role) => self::roleMustHaveAtLeastOneUser($role));
                $model->roles()->detach();
            }
        });

        // if user must have at least one role
        ModelHasRole::deleting(function (ModelHasRole $modelHasRole) {
            $model = $modelHasRole->pivotParent;
            if ($model->roles()->count() === 1 && $model instanceof MustHasAtLeastOneRole) {
                throw new MustHaveAtLeastOneRoleException($model);
            }
        });

        Role::deleting(function (Role $role) {
            foreach ($role->users as $user) {
                if ($user->roles()->count() === 1 && $user instanceof MustHasAtLeastOneRole) {
                    throw new MustHaveAtLeastOneRoleException($user);
                }
            }
        });

        User::creating(function (User $user) {
            if ($user instanceof MustHasAtLeastOneRole) {
                $user->assignRole($user->defaultRole());
            }
        });

        // if role must have at least one user
        ModelHasRole::deleting(function (ModelHasRole $modelHasRole) {
            try {
                $role = $modelHasRole->role;
                self::roleMustHaveAtLeastOneUser($role);
            } catch (EnumCaseNotFoundException) {

            }
        });


        // auto register permissions to unlimited access roles
        Permission::created(function ($permission) {
            UnlimitedAccessRole::all()
                ->each(function (UnlimitedAccessRole $unlimitedAccessRole) use ($permission) {
                    $unlimitedAccessRole->role->givePermissionTo($permission);
                    $unlimitedAccessRole->permissions()->updateOrCreate([
                        "role_id" => $unlimitedAccessRole->role->id,
                        "permission_id" => $permission->id
                    ]);
                });
        });

        // when deleting a permission, need to delete in the unlimited access protection first
        Permission::deleting(function ($permission) {
            UnlimitedAccessRolePermission::query()
                ->where("permission_id", $permission->id)
                ->delete();
        });

        // auto register to unlimited_access_role_permissions when unlimited_access_role is created
        UnlimitedAccessRole::created(function (UnlimitedAccessRole $unlimitedAccessRole) {
            $unlimitedAccessRole->permissions()->createMany(Permission::all()->map(function (Permission $permission) use ($unlimitedAccessRole) {
                return [
                    "role_id" => $unlimitedAccessRole->role->id,
                    "permission_id" => $permission->id
                ];
            }));
        });
    }

    private function roleMustHaveAtLeastOneUser(Role $role)
    {
        $defaultRole = $role->default_role;
        if (null !== $defaultRole &&
            $role->users()->count() === 1 &&
            in_array(DefaultRole::guess($defaultRole, false)->value, array_map(fn($v) => $v->value, config('permission.modifiers.must_have_at_least_one_user')))
        ) {
            throw new MustHaveAtLeastOneUserException($role);
        }
    }
}
