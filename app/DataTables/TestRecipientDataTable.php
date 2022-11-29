<?php

namespace App\DataTables;

use App\Models\User;
use App\Services\RoleRenderService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Models\TestRecipient;

class TestRecipientDataTable extends DataTable
{
    const TABLE_NAME = "push_notifications_test_recipients_table";

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
            ->rawColumns(["selected", "roles"])
            ->addColumn("selected", function (User $user) {
                return "<label class='form-check form-check-custom form-check-solid d-flex flex-center'>".
                    "<input type='checkbox' name='selected[]' value='" . $user->id . "' data-name='" . $user->name . "' class='form-check-input test-recipients-checkbox' " . ($user->isTestRecipient() ? 'checked' : '') . ">".
                    "</label>";
            })
            ->filterColumn("selected", function ($query, $keyword) {
                $query->whereHas("roles", function ($query) use ($keyword) {
                    $query->where("roles.name", "like", "%{$keyword}%");
                });
            })
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
     * @param TestRecipient $model
     * @return Builder
     */
    public function query(TestRecipient $model): Builder
    {
        return User::with("roles")->select([
            "id",
            "name",
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
            ->orders([0, "desc"])
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
            Column::make("selected")
                ->searchable(false)
                ->title(__("notification.manage_test_recipients.table_header.is_test_recipient"))
                ->exportable(false)
                ->printable(false)
                ->width("120px")
                ->addClass("text-center"),
            Column::make("name")
                ->title(__("notification.manage_test_recipients.table_header.name")),
            Column::make("roles")
                ->title(__("notification.manage_test_recipients.table_header.roles")),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return "TestRecipient_" . date("YmdHis");
    }
}
