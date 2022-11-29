<?php

namespace App\DataTables\Permissions;

use App\Services\RoleRenderService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use function __;
use function collect;
use function datatables;
use function str;
use function trans;

class PermissionRoleDataTable extends DataTable
{
    public const TABLE_NAME = "permissions_table";

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     */
    public function dataTable(mixed $query): DataTableAbstract
    {
        return datatables()
            ->eloquent($query)
            ->rawColumns(["roles"])

            // class
            ->filterColumn("class", function ($query, $keyword) {
                $classRawQuery = "SUBSTRING_INDEX(SUBSTRING_INDEX(`permissions`.`name`, '::', 1), '\\\', -1)";
                $modules = __("role.show.permissions_tab.permission_modules");
                $caseStatement = "CASE $classRawQuery" .
                    collect($modules)->map(function ($string, $module) {
                        return " WHEN '$module' THEN '$string' ";
                    })->join("")
                    . "ELSE $classRawQuery END";
                $query->whereRaw("$caseStatement like ?", ["%{$keyword}%"]);
            })

            // permissions
            ->editColumn("permission", function ($row) {
                $permissionName = $row->permission;
                return trans()->has("role.show.permissions_tab.permission_mapping." . $permissionName)
                    ? __("role.show.permissions_tab.permission_mapping." . $permissionName) : str($permissionName)->snake()->title()->replace("_", " ");
            })
            ->filterColumn("permission", function ($query, $keyword) {
                $permissionRawQuery = "SUBSTRING_INDEX(`permissions`.`name`, '::', -1)";
                $permissions = __("role.show.permissions_tab.permission_mapping");
                $caseStatement = "CASE $permissionRawQuery" .
                    collect($permissions)->map(function ($string, $permission) {
                        return " WHEN '$permission' THEN '$string' ";
                    })->join("")
                    . "ELSE $permissionRawQuery END";
                $query->whereRaw("$caseStatement like ?", ["%{$keyword}%"]);
            })

            // roles
            ->editColumn("roles", function ($model) {
                return RoleRenderService::render($model->roles);
            })
            ->filterColumn("roles", function ($query, $keyword) {
                $query->whereHas("roles", function ($query) use ($keyword) {
                    $query->where("roles.name", "like", "%{$keyword}%");
                });
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Permission $model
     * @return Builder
     */
    public function query(Permission $model): Builder
    {
        $classRawQuery = "SUBSTRING_INDEX(SUBSTRING_INDEX(`permissions`.`name`, '::', 1), '\\\', -1)";
        $modules = __("role.show.permissions_tab.permission_modules");
        $caseStatement = "CASE $classRawQuery" .
            collect($modules)->map(function ($string, $module) {
                return " WHEN '$module' THEN '$string' ";
            })->join("")
            . "ELSE $classRawQuery END";

        return $model->newQuery()->with("roles")
            ->select([
                DB::raw($caseStatement . ' as `class`'),
                DB::raw("SUBSTRING_INDEX(`permissions`.`name`, '::', -1) as `permission`"),
                'permissions.*'
            ]);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): \Yajra\DataTables\Html\Builder
    {
        return $this->builder()
            ->setTableId(self::TABLE_NAME)
            ->columns($this->getColumns())
            ->pageLength(50)
            ->lengthMenu([
                [10, 25, 50, 100, -1],
                ["10", "25", "50", "100", __("general.message.all")]
            ])
            ->minifiedAjax()
            ->stateSave(false)
            ->responsive()
            ->autoWidth(false)
            ->parameters([
                /*"drawCallback" => "function() {
                    $('a, button, td.first-column').on('click', function() {
                        event.stopPropagation();
                    })
                }",
                "rowCallback" => "function (row, data) {
                    $(row).addClass('bg-hover-secondary');
                    $(row).click(function () {
                        window.location.href = '" . route("user.edit", ["user_id" => ":id"]) . "'.replace(':id', data.id);
                    })
                }",*/
                "initComplete" => "function() {
                    $('#" . $this->builder()->getTableId() . "').bind('DOMNodeInserted', function(e) {
                        KTMenu.createInstances();
                        reinitDataTableTooltips('" . $this->builder()->getTableId() . "');
                    });
                }",
                "drawCallback" => "function() { KTMenu.createInstances(); reinitDataTableTooltips('" . $this->builder()->getTableId() . "'); }",
                "dom" => "<'table-responsive'tr><'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
            ])
            ->addTableClass("align-middle table-row-dashed fs-6 gy-5");
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return [
            Column::make("class")
                ->title(__("permission.index.table_header.module")),
            Column::make("permission")
                ->title(__("permission.index.table_header.permission")),
            Column::make("roles")
                ->title(__("permission.index.table_header.roles"))
                ->width("50%")
                ->orderable(false),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return "PermissionRole_" . date("YmdHis");
    }
}
