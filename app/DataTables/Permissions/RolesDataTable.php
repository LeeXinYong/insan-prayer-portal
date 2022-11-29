<?php

namespace App\DataTables\Permissions;

use App\Models\ProtectedRole;
use App\Services\DataTableRenderHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use function __;
use function auth;
use function datatables;
use function route;
use function view;

class RolesDataTable extends DataTable
{
    public const TABLE_NAME = "roles_table";

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
            ->rawColumns(["action", "name"])
            ->editColumn("name", function ($role) {
                return
                    DataTableRenderHelper::renderMultipleFields($role, [
                        fn() => DataTableRenderHelper::renderColor($role, noText: true),
                        "name" => fn($name, $role) => DataTableRenderHelper::renderTitle($role, route('role.show', ["role" => $role]), 'name', 'View') .
                            (auth()->user()->hasRole($role) ? "<span class='badge badge-light-info badge-pill badge-sm ms-1'>" . __("role.index.table_util.you") . "</span>" : ""),
                    ], flexColumn: false, gap: 1);
            })
            ->addColumn("action", function (Role $role) {
                $actions = [
                    [
                        "url" => route("role.show", ["role" => $role]),
                        "label" => __("role.index.button.view_role"),
                        "disabled" => Auth::user()->cannotView(Role::class)
                    ],
                    [
                        "url" => route("role.show.users", ["role" => $role]),
                        "label" => __("role.index.button.view_users"),
                        "disabled" => Auth::user()->cannotViewUsers(Role::class)
                    ],
                ];

                $forceDropdown = true;

                return view("pages.common-components.buttons.action-menu-button", compact("actions", "forceDropdown"));
            });

    }

    /**
     * Get query source of dataTable.
     *
     * @param Role $role
     * @return Builder
     */
    public function query(Role $role): Builder
    {
        return $role->newQuery()
            ->addSelect([
                "number_of_users" => function($query) {
                    return $query->select(DB::raw("count(*)"))
                        ->from("model_has_roles")
                        ->whereColumn("model_has_roles.role_id", "roles.id");
                },
                "number_of_permissions" => function($query) {
                    return $query->select(DB::raw("count(*)"))
                        ->from("role_has_permissions")
                        ->whereColumn("role_has_permissions.role_id", "roles.id");
                }
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
            Column::make("name")
                ->title(__("role.index.table_header.name"))
                ->width("65%")
                ->responsivePriority(-1),
            Column::computed("number_of_users")
                ->title(__("role.index.table_header.number_of_users"))
                ->width("15%"),
            Column::computed("number_of_permissions")
                ->title(__("role.index.table_header.number_of_permissions"))
                ->width("15%"),
            Column::computed("action")
                ->title(__("role.index.table_header.action"))
                ->width("5%")
                ->responsivePriority(-1),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return "Roles_" . date("YmdHis");
    }
}
