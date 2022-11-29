<?php

namespace App\DataTables;

use App\Models\EmailTemplate;
use App\Services\DataTableRenderHelper;
use App\Services\DateTimeFormatterService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EmailTemplatesDataTable extends DataTable
{
    public const TABLE_NAME = "emailtemplate_table";

    /**
     * Build DataTable class.
     *
     * @param  mixed  $query  Results from query() method.
     *
     * @return DataTableAbstract
     */
    public function dataTable(mixed $query): DataTableAbstract
    {
        return datatables()
            ->eloquent($query)
            ->rawColumns(["action", "name", "updated_at"])
            ->addColumn("action", function (EmailTemplate $model) {
                $actions = array(
                    "edit" => array(
                        "url" => route("system.settings.emailtemplate.edit", ["emailtemplate" => $model->id]),
                        "label" => __("general.button.edit"),
                        "disabled" => Auth::user()->cannotUpdate(EmailTemplate::class)
                    )
                );

                $forceDropdown = true;

                return view("pages.common-components.buttons.action-menu-button", compact("actions", "forceDropdown"));
            })
            ->editColumn("name", function(EmailTemplate $model){
                return DataTableRenderHelper::renderTitle($model, route("system.settings.emailtemplate.edit", ["emailtemplate" => $model->id]), "name");
            })
            ->editColumn("updated_at", function (EmailTemplate $model) {
                return DataTableRenderHelper::renderDateTime($model);
            })
            ->filterColumn("updated_at", DataTableRenderHelper::filterDateTime());
    }

    /**
     * Get query source of dataTable.
     *
     * @param EmailTemplate $model
     *
     * @return Builder
     */
    public function query(EmailTemplate $model): Builder
    {
        return $model->newQuery();
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
                        window.location.href = '" . route("banner.edit", ["banner" => ":id"]) . "'.replace(':id', data.id);
                    })
                }",*/
                "order" => [[ 0, 'asc' ]],
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
            Column::make("name")
                ->title(__("settings.email_template.table_header.email_template"))
                ->width("20%")
                ->responsivePriority(-1),
            Column::make("subject")
                ->title(__("settings.email_template.table_header.email_subject"))
                ->width("20%"),
            Column::make("description")
                ->title(__("settings.email_template.table_header.description"))
                ->width("45%"),
            Column::make("updated_at")
                ->title(__("settings.email_template.table_header.last_updated"))
                ->width("10%"),
            Column::computed("action")
                ->title(__("settings.email_template.table_header.action"))
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
        return "Email Templates_".date("YmdHis");
    }
}
