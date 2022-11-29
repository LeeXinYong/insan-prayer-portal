<?php

namespace App\Http\Controllers;

use App\DataTables\Permissions\RolesDataTable;
use App\DataTables\Permissions\RoleUserDataTable;
use App\Exceptions\MustHaveAtLeastOneUserException;
use App\Exceptions\RoleModifiers\MustHaveAtLeastOneRoleException;
use App\Exceptions\RoleUpdateException;
use App\Http\Requests\StripTagRequest as Request;
use App\Models\ProtectedRole;
use App\Models\UnlimitedAccessRole;
use App\Models\User;
use App\Services\ValidationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class RoleController extends Controller
{
    public const DATATABLE_TYPE_ROLE_USER = 'user';

    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     *
     * @param RolesDataTable $dataTable
     * @return mixed
     */
    public function index(RolesDataTable $dataTable): mixed
    {
        return $dataTable->render("pages.role.index");
    }

    public function show(Request $request, Role $role): mixed
    {
        if ($request->ajax()) {
            $moduleRawQuery = "SUBSTRING_INDEX(SUBSTRING_INDEX(`permissions`.`name`, '::', 1), '\\\', -1)";
            $modules = __("role.show.permissions_tab.permission_modules");
            $caseStatement = "CASE $moduleRawQuery" .
                collect($modules)->map(function ($string, $module) {
                    return " WHEN '$module' THEN '$string' ";
                })->join("")
                . "ELSE $moduleRawQuery END";

            $permissions = Permission::query()
                ->select([
                    'id',
                    DB::raw($caseStatement . ' as `module`'),
                    DB::raw("SUBSTRING_INDEX(`permissions`.`name`, '::', 1) as `class`"),
                    DB::raw("SUBSTRING_INDEX(`permissions`.`name`, '::', -1) as `permission`"),
                    "granted" => function (\Illuminate\Database\Query\Builder $query) use ($role) {
                        return $query->select(DB::raw(1))
                            ->from("role_has_permissions")
                            ->where("role_has_permissions.role_id", "=", $role->id)
                            ->whereColumn("role_has_permissions.permission_id", "=", "permissions.id");
                    }
                ]);

            $permissions = DB::query()
                ->fromSub($permissions, "permissions")
                ->select([
                    'id',
                    "module",
                    "class",
                    DB::raw("CONCAT('[', GROUP_CONCAT(
                      JSON_OBJECT(
                        'permission', permission,
                        'granted', granted
                      )
                    ) ,  ']') AS permission_list")
                ])
                ->groupBy("module", "class")
                ->get()
                ->map(function ($permission) use ($role) {
                    $permissionList = json_decode($permission->permission_list, true);

                    // user can only view a module action if they can perform the action on the module
                    $permission->permission_list = array_filter($permissionList, function ($permissible) use ($permission, $role) {
                        return Auth::user()->can("viewPermission", [$role, $permission->class, $permissible["permission"]]);
                    });

                    // user can only update a module action if they can perform the action on the module and they can update the role and the role is not unlimited access role
                    $permission->permission_list = array_map(function ($permissible) use ($permission, $role) {
                        $permissible['updatable'] = Auth::user()->can("updatePermission", [$role, $permission->class, $permissible["permission"]]);
                        $permissible['name'] = trans()->has("role.permissions.permission_mapping." . $permissible['permission']) ? __("role.permissions.permission_mapping." . $permissible['permission']) : str($permissible['permission'])->snake()->title()->replace("_", " ");

                        return $permissible;
                    }, $permission->permission_list);

                    $permission->updatable = collect($permission->permission_list)->every(fn($permissible) => $permissible['updatable']);

                    $permission->name = trans()->has("role.permissions.permission_modules." . $permission->module) ? __("role.permissions.permission_modules." . $permission->module) : str($permission->module)->snake()->title()->replace("_", " ");

                    return $permission;
                })
                ->filter(fn($permission) => count($permission->permission_list) > 0);

            $allPermissionsSelected = $permissions->every(function ($permission) {
                return collect($permission->permission_list)->every(fn($p) => $p['granted']);
            });

            $noPermissionUpdatable = !$permissions->some(function ($permission) {
                return collect($permission->permission_list)->some(fn($p) => $p['updatable']);
            });

            return response()->json(compact("permissions", "allPermissionsSelected", "noPermissionUpdatable"));
        }

        return view("pages.role.show", compact("role"));
    }

    public function showUsers(Request $request, RoleUserDataTable $dataTable, Role $role): mixed
    {
        return $dataTable->render("pages.role.show_users", compact("role"));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator("role", "edit", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $role = new Role();
            $role->name = $request->get("name");
            $role->color = $request->get("color");
            $role->save();
            DB::commit();
            return response()->json([
                "message" => __("role.index.addRole.success"),
                "data" => [
                    "name" => $role->name
                ]
            ]);
        } catch (Exception $e) {
            catchException($e);
            return response()->json(["error" => __("role.index.addRole.error")], 500);
        }
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        $validator = ValidationService::getValidator("role", "edit", data: ["id" => $role->id]);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $errorMessage = __("general.message.please_try_again");

            try {
                DB::beginTransaction();
                $role->name = request()->get("name");
                $role->color = $request->get("color");
                if (!$role->save()) {
                    throw new RoleUpdateException(__("role.show.editName.error"));
                }
                DB::commit();
                return response()->json([
                    "message" => __("role.show.editName.success"),
                    "data" => [
                        "name" => $role->name
                    ]
                ]);

            } catch (RoleUpdateException $e) {
                catchException($e);
                abort(500, $e->getMessage());
            }  catch (Exception|Throwable|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
                catchException($e);
                return response()->json(["error" => $errorMessage], 500);
            }
        }
    }

    public function updatePermissions(Request $request, Role $role)
    {
        abort_unless(Auth::user()->hasPermissionTo(Role::class . "::updatePermission"), 403);
        $validator = ValidationService::getValidator("role", "updatePermissions", data: ["role" => $role]);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $role->syncPermissions($request->get("permissions"));
            DB::commit();

            return response()->json([
                "success" => __("role.show.update_permissions.success"),
                "button" => __("general.button.ok"),
                "redirect" => route("role.show", ["role" => $role])
            ]);
        } catch (\Exception $e) {
            catchException($e);
            abort(500, __("general.message.please_try_again"));
        }
    }

    /**
     * Update user for role
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function updateUser(Request $request, Role $role): JsonResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail(request()->get("userReference"));

        abort_unless(Auth::user()->canUpdateUsers($role, $user), 403);

        $validator = ValidationService::getValidator("role", "updateUser", data: ["role" => $role]);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $grantingUser = request()->get("grantingUser");
            if ($grantingUser) {
                $errorMessage = __("role.users.change_user.failed_to_grant_role");
                $successMessage = __("role.users.change_user.role_granted");
                $user->assignRole($role);
            } else {
                $errorMessage = __("role.users.change_user.failed_to_revoke_role");
                $successMessage = __("role.users.change_user.role_revoked");
                $user->removeRole($role);
            }
            DB::commit();
            return response()->json([
                "message" => $successMessage,
            ]);

//            return response()->json([
//                "success" => __("role.show.update_permissions.success"),
//                "button" => __("general.button.ok"),
//                "redirect" => route("role.show", ["role" => $role])
//            ]);
        } catch (\Exception $e) {
            $errorMessage ??= __("general.message.please_try_again");
            if ($e instanceof MustHaveAtLeastOneRoleException || $e instanceof MustHaveAtLeastOneUserException) {
                $errorMessage .= " " . $e->getMessage();
            }
            catchException($e);
            abort(500, $errorMessage ?? __("general.message.please_try_again"));
        }
    }

    public function destroy(Request $request, Role $role): JsonResponse|RedirectResponse
    {
        // Check the delete request origin
        // If request input not empty and "_token" exists, means from form
        // Else, means from index data-destroy axios delete
        $request_type = !empty($request->all()) && isset($request->_token);

        try {
            DB::beginTransaction();
            $role->delete();
            DB::commit();

            // Log Audit
            LoggerController::log("roles", $role, "audit_log.message.delete_role", $role->name, $role->toArray());

            if ($request_type) {
                return redirect(route("role.index"))->with("message", __("role.delete.success", ["name" => $role->name]));
            } else {
                return response()->json(["message" => [__("role.delete.success", ["name" => $role->name])]]);
            }
        } catch (Exception $e) {
            catchException($e);
            $errorMessage = __("role.delete.failed");
            if (ProtectedRole::query()->where("role_id", $role->id)->exists()) {
                $errorMessage = __("role.delete.protected");
            }

            if ($e instanceof MustHaveAtLeastOneRoleException) {
                $errorMessage .= ". " . $e->getMessage();
            }

            if ($request_type) {
                return redirect()->back()->withErrors(["error" => $errorMessage]);
            } else {
                return response()->json(["error" => [$errorMessage]], 422);
            }
        }
    }

    public static function canViewUsers(User $user): bool
    {
        return $user->canViewAny(User::class);
    }

    public static function canUpdateUsers(User $user, Role $role, User $targetUser): bool
    {
        return $user->canUpdate($role) && $user->isNot($targetUser);
    }

    public static function canViewPermission(User $user, Role $role, $module, $action): bool
    {
        $action = Arr::wrap($action);
        foreach ($action as $a) {
            if (!$user->hasPermissionTo($module. "::". $a)) {
                return false;
            }
        }
        return $user->canView($role);
    }

    public static function canUpdatePermission(User $user, Role $role, $module, $action): bool
    {
        if (UnlimitedAccessRole::query()->where("role_id", "=", $role->id)->exists()) {
            return false;
        }
        $action = Arr::wrap($action);
        foreach ($action as $a) {
            if (!$user->hasPermissionTo($module. "::". $a)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Update name of role
     * @deprecated
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private static function updateName($role, &$errorMessage): JsonResponse
    {
        $errorMessage = __("role.show.editName.error");
        $role->name = request()->get("name");
        $role->save();
        DB::commit();
        return response()->json([
            "message" => __("role.show.editName.success"),
            "data" => [
                "name" => $role->name
            ]
        ]);
    }

    /**
     * Update permission for role
     * @deprecated
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    private static function updatePermission($role, &$errorMessage): JsonResponse
    {
        $grantingPermission = request()->get("grantingPermission");
        if ($grantingPermission) {
            $errorMessage = __("role.show.permissions_tab.change_permission.failed_to_grant_permission");
            $successMessage = __("role.show.permissions_tab.change_permission.permission_granted");
            $role->givePermissionTo(request()->get("permissionReference"));
        } else {
            $errorMessage = __("role.show.permissions_tab.change_permission.failed_to_revoke_permission");
            $successMessage = __("role.show.permissions_tab.change_permission.permission_revoked");
            $role->revokePermissionTo(request()->get("permissionReference"));
        }
        DB::commit();
        return response()->json([
            "message" => $successMessage,
        ]);
    }

    /**
     * Update all permissions of a module for role
     * @deprecated
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    private static function updateModulePermission($role, &$errorMessage): JsonResponse
    {
        $permissions = Permission::query()
            ->whereRaw('SUBSTRING_INDEX(`permissions`.`name`, "::", 1) = ?', [request()->get("moduleReference")])
            ->get();

        $grantingPermission = request()->get("grantingModulePermissions");

        if ($grantingPermission) {
            $errorMessage = __("role.show.permissions_tab.batch_change.failed_to_grant_permissions");
            $successMessage = __("role.show.permissions_tab.batch_change.permissions_granted");
            $role->givePermissionTo($permissions);
        } else {
            $errorMessage = __("role.show.permissions_tab.batch_change.failed_to_revoke_permissions");
            $successMessage = __("role.show.permissions_tab.batch_change.permissions_revoked");
            $role->revokePermissionTo($permissions);
        }

        DB::commit();
        return response()->json([
            "message" => $successMessage,
        ]);
    }

    public static function getRoles()
    {
        return Role::all();
    }

}
