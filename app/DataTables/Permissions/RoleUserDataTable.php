<?php

namespace App\DataTables\Permissions;

use App\Http\Controllers\RoleController;
use App\Models\User;
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

class RoleUserDataTable extends DataTable
{
    public const TABLE_NAME = "role_user_table";

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
            ->rawColumns(["granted"])
            ->editColumn("granted", function ($row) {
                $userId = $row->id;
                $userGranted = $row->granted;
                return "<div class='form-check form-check-custom form-check-solid form-check-sm ".($this->canUserUpdate($row) ? '' : 'cursor-not-allowed ')."'>" .
                    "<input class='form-check-input user-checkbox' type='checkbox' value='' id='user-checkbox-" . $userId . "' data-reference='" . $userId . "'" .
                    ($userGranted ? 'checked ' : '') .
                    ($this->canUserUpdate($row) ? '' : 'disabled ') .
                    "/>" .
                    "</div>";
            });
    }

    private function canUserUpdate($row)
    {
        return Auth::user()->can("updateUsers", [$this->request()->role, $row]);
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        $role = $this->request()->role;
        return User::query()
            ->select([
                "users.id",
                "users.name",
                "users.email",
                "granted" => function (\Illuminate\Database\Query\Builder $query) use ($role) {
                    return $query->select(DB::raw(1))
                        ->from("model_has_roles")
                        ->where("model_has_roles.role_id", "=", $role->id)
                        ->where("model_has_roles.model_type", "=", User::class)
                        ->whereColumn("model_has_roles.model_id", "=", "users.id");
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
        $parameters = [
            "type" => RoleController::DATATABLE_TYPE_ROLE_USER
        ];

        return $this->builder()
            ->orders([0, "desc"])
            ->setTableId(self::TABLE_NAME)
            ->columns($this->getColumns())
            ->pageLength(50)
            ->minifiedAjax('', null, $parameters)
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
                "drawCallback" => "function() { KTMenu.createInstances(); }",
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
            Column::make("granted")
                ->title(__("role.users.table_header.granted"))
                ->width(100)
                ->searchable(false)
                ->exportable(false)
                ->printable(false),
            Column::make("name")
                ->title(__("role.users.table_header.name")),
            Column::make("email")
                ->title(__("role.users.table_header.email")),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return "RoleUser_" . date("YmdHis");
    }
}
