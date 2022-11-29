<?php

namespace App\DataTables;

use App\Models\User;
use App\Services\DataTableRenderHelper;
use App\Services\DateTimeFormatterService;
use App\Services\RoleRenderService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    public const TABLE_NAME = "user_table";

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
            ->rawColumns(["action", "name", "last_login", "status", "role"])
            ->addColumn("action", function (User $model) {
                $actions = [
                    "show" => [
                        "url" => route("user.show", ["user" => $model->id]) . "#activities",
                        "label" => __("general.button.view"),
                        "disabled" => Auth::user()->cannotView(User::class)
                    ],
                ];

                $forceDropdown = true;

                return view("pages.common-components.buttons.action-menu-button", compact("actions", "forceDropdown"));
            })
            ->addColumn("role", function (User $model) {
                return RoleRenderService::render($model->roles);
            })
            ->editColumn("name", function (User $model) {
                return DataTableRenderHelper::renderMultipleFields($model, [
                    "name" => fn ($_) => DataTableRenderHelper::renderTitle($model, route("user.show", ["user" => $model->id]), column: "name", ability: "View"),
                    "email" => fn ($email) => DataTableRenderHelper::renderMutedField($email),
                ]);
            })
            ->editColumn("last_login", function (User $model) {
                return DataTableRenderHelper::renderDateTime($model, "last_login");
            })
            ->filterColumn("last_login", DataTableRenderHelper::filterDateTime("last_login"))
            ->editColumn("status", function (User $model) {
                return DataTableRenderHelper::renderBadge($model);
            })
            ->filterColumn("status", function ($query, $keyword) {
                $values = [
                    0 => "Inactive",
                    1 => "Active",
                ];
                $sql = "CASE";
                foreach ($values as $key => $value) {
                    $sql .= " WHEN status = $key THEN '$value'";
                }
                $sql .= " END like ?";
                $query->whereRaw($sql, ["%$keyword%"]);
            })
            ->filterColumn("user_roles", function ($query, $keyword) {
                $roleUsers = Role::query()
                    ->select([
                        DB::raw('GROUP_CONCAT(name) as name'),
                        "model_id",
                    ])
                    ->join("model_has_roles", "model_has_roles.role_id", "=", "roles.id")
                    ->where("model_type", User::class)
                    ->groupBy("model_id");

                $query->whereExists(function ($query) use ($roleUsers, $keyword) {
                    $query->select(DB::raw(1))
                        ->fromSub($roleUsers, "role_users")
                        ->whereColumn("role_users.model_id", "=", "users.id")
                        ->whereRaw("LOWER(role_users.name) like ?", ["%$keyword%"]);
                });
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return Builder
     */
    public function query(User $model): Builder
    {
        $request = $this->request();

        if ($request->has('filter_roles') && $request->get('filter_roles') != null) {
            $roles = $request->get('filter_roles');
            return $model->role($roles);
        }

        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): \Yajra\DataTables\Html\Builder
    {
        $parameters = [
            "filter_roles" => "$('.role_filter:checked').map(function(){ return $(this).val(); }).get();"
        ];

        // for parameters that is not used for filtering
        $non_filter_parameters = [];

        // for parameters that is used for filtering - retrieve from different between non-filter parameters and all parameters
        $filter_parameters = json_encode(array_diff(array_keys($parameters), $non_filter_parameters));

        return $this->builder()
            ->setTableId(self::TABLE_NAME)
            ->columns($this->getColumns())
            ->pageLength(50)
            ->lengthMenu([
                [10, 25, 50, 100, -1],
                ["10", "25", "50", "100", __("general.message.all")]
            ])
            ->minifiedAjax("", null, $parameters)
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
            Column::computed("role")
                ->title(__("user.table_header.role"))
                ->width("8%"),
            Column::make("name")
                ->title(__("user.table_header.name"))
                ->width("67%")
                ->responsivePriority(-1),
            Column::make("status")
                ->title(__("user.table_header.status"))
                ->width("10%"),
            Column::make("last_login")
                ->title(__("user.table_header.last_login"))
                ->addClass('align-top')
                ->width("10%"),
            Column::computed("action")
                ->title(__("user.table_header.action"))
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
        return "Users_" . date("YmdHis");
    }
}
