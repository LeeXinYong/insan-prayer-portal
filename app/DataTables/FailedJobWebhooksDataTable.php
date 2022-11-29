<?php

namespace App\DataTables;

use App\Models\FailedJobWebhook;
use App\Services\DataTableRenderHelper;
use App\Services\DateTimeFormatterService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FailedJobWebhooksDataTable extends DataTable
{
    public const TABLE_NAME = "failed_job_webhook_table";

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
            ->rawColumns(["action", "endpoint", "last_called"])
            ->addColumn("action", function (FailedJobWebhook $model) {
                $actions = [
                    "edit" => [
                        "label" => __("settings.failed_job_webhook.message.send_test_webhook"),
                        "classes" => Auth::user()->canTest(FailedJobWebhook::class) ? "edit-control" : "",
                        "disabled" => Auth::user()->cannotTest(FailedJobWebhook::class),
                        "data" => [
                            "eid" => $model->id,
                            "action" => route("system.settings.failed_job_webhook.test", $model->id),
                            "bs-original-title" => "'" . __("settings.failed_job_webhook.message.send_test_webhook") . "'"

                        ]
                    ],
                    "refresh" => [
                        "label" => __("settings.failed_job_webhook.message.regenerate_secret_key"),
                        "classes" => Auth::user()->canRefreshSecretKey(FailedJobWebhook::class) ? "edit-control" : "",
                        "disabled" => Auth::user()->cannotRefreshSecretKey(FailedJobWebhook::class),
                        "data" => [
                            "eid" => $model->id,
                            "action" => route("system.settings.failed_job_webhook.regenerateSecretKey", $model->id),
                            "bs-original-title" => "'" . __("settings.failed_job_webhook.message.regenerate_secret_key") . "'",
                            "text" => "'" . __("settings.failed_job_webhook.message.are_you_sure_to_regenerate_secret_key") . "'"
                        ]
                    ],
                    "delete" => [
                        "label" => __("general.button.delete"),
                        "classes" => Auth::user()->canDelete(FailedJobWebhook::class) ? "delete-control" : "",
                        "disabled" => Auth::user()->cannotDelete(FailedJobWebhook::class),
                        "data" => [
                            "eid" => $model->id,
                            "action" => route("system.settings.failed_job_webhook.destroy", $model->id),
                            "method" => "delete",
                            "bs-original-title" => "'" . __("settings.failed_job_webhook.message.delete_webhook") . "'",
                            "text" => "'" . __("settings.failed_job_webhook.message.are_you_sure_to_delete_webhook") . "'"
                        ]
                    ]
                ];

                $forceDropdown = true;

                return view("pages.common-components.buttons.action-menu-button", compact("actions", "forceDropdown"));
            })
            ->editColumn("endpoint", function (FailedJobWebhook $model) {
                return "<span data-bs-toggle='tooltip' title='' data-bs-original-title='". $model->endpoint . "'  aria-label='" . $model->endpoint . "'>" . $model->endpoint . "</span>";
            })
            ->editColumn("last_called", function (FailedJobWebhook $model) {
                return DataTableRenderHelper::renderDateTime($model, "last_called");
            })
            ->filterColumn("last_called", DataTableRenderHelper::filterDateTime("last_called"));
    }

    /**
     * Get query source of dataTable.
     *
     * @param FailedJobWebhook $model
     *
     * @return Builder
     */
    public function query(FailedJobWebhook $model): Builder
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
            ->minifiedAjax(route("system.settings.failed_job_webhook.index"))
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
                "order" => [[ 0, 'desc' ]],
                "initComplete" => "function() {
                    $('#" . $this->builder()->getTableId() . "').bind('DOMNodeInserted', function(e) {
                        KTMenu.createInstances();
                    });
                }",
                "drawCallback" => "function() {
                    KTMenu.createInstances();
                    reinitDataTableTooltips('" . $this->builder()->getTableId() . "');
                }",
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
            Column::make("endpoint")
                ->title(__("settings.failed_job_webhook.table_header.webhook_url"))
                ->width("85%")
                ->responsivePriority(-1),
            Column::make("last_called")
                ->title(__("settings.failed_job_webhook.table_header.last_called"))
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
        return "Failed Job Webhooks_".date("YmdHis");
    }
}
