<?php

namespace App\DataTables\Permissions;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Html\Column;
use function __;
use function request;

class UserPermissionsDataTable extends PermissionRoleDataTable
{
    public const TABLE_NAME = "users_permissions_table";

    /**
     * Get query source of dataTable.
     *
     * @param Permission $model
     * @return Builder
     */
    public function query(Permission $model): Builder
    {
        $user = $this->user();

        $moduleRawQuery = "SUBSTRING_INDEX(SUBSTRING_INDEX(`permissions`.`name`, '::', 1), '\\\', -1)";
        $modules = __("role.show.permissions_tab.permission_modules");
        $caseStatement = "CASE $moduleRawQuery" .
            collect($modules)->map(function ($string, $module) {
                return " WHEN '$module' THEN '$string' ";
            })->join("")
            . "ELSE $moduleRawQuery END";

        return $model->newQuery()
            ->with("roles", function($query) use ($user) {
                $query->whereExists(function($query) use ($user) {
                    $query->select(DB::raw(1))
                        ->from("model_has_roles")
                        ->whereColumn("model_has_roles.role_id", "roles.id")
                        ->where("model_has_roles.model_id", $user->id);
                });
            })
            ->whereHas("roles", function($query) use ($user) {
                $query->whereExists(function($query) use ($user) {
                    $query->select(DB::raw(1))
                        ->from("model_has_roles")
                        ->whereColumn("model_has_roles.role_id", "roles.id")
                        ->where("model_has_roles.model_id", $user->id);
                });
            })
            ->select([
                DB::raw($caseStatement . " as `module`"),
                DB::raw("SUBSTRING_INDEX(`permissions`.`name`, '::', 1) as `class`"),
                DB::raw("SUBSTRING_INDEX(`permissions`.`name`, '::', -1) as `permission`"),
                "permissions.*",
            ]);
    }

    private function user() : User
    {
        /** @var User $user */
        $user = request()->route()->parameter("user");
        return $user;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return [
            Column::make("module")
                ->title(__("user.user_permissions_table_header.module")),
            Column::make("permission")
                ->title(__("user.user_permissions_table_header.permission")),
            Column::make("roles")
                ->title(__("user.user_permissions_table_header.granted_by_role"))
                ->width("50%")
                ->orderable(false),
        ];
    }
}
