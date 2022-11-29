<?php

namespace App\DataTables\Logs;

use App\Models\BackupLog;
use App\Services\DateTimeFormatterService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BackupLogsDataTable extends DataTable
{
    public const TABLE_NAME = "backupLog_table";

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
            ->rawColumns(["created_at", "status", "action", "stack_trace"])
            ->addColumn("action", function (BackupLog $model) {
                $actions = array(
                    "delete" => [
                        "url" => route("system.log.backup.destroy", $model->id),
                        "label" => __("general.button.delete"),
                        "disabled" => Auth::user()->cannotDelete(BackupLog::class)
                    ]
                );

                $forceDropdown = true;

                return view("pages.common-components.buttons.action-menu-button", compact("actions", "forceDropdown"));
            })
            ->editColumn("created_at", function (BackupLog $model) {
                return DateTimeFormatterService::formatModalDateTime($model->created_at);
            })
            ->editColumn("status", function (BackupLog $model) {
                $styles = [
                    0 => "danger",
                    1 => "success",
                ];
                $style = $styles[$model->status] ?? "info";

                $values = [
                    0 => __("general.message.failed"),
                    1 => __("general.message.success"),
                ];
                $value = $values[$model->status] ?? "Unknown";

                return "<div class='badge badge-light-$style fw-bolder'>$value</div>";
            })
            ->editColumn("stack_trace", function (BackupLog $model) {
                $content = $model->stack_trace;

                if($content != "") {
                    return view("pages.log.backup._details", compact("content"));
                }

                return $content;
            })
            ->filterColumn("created_at", function ($query, $keyword) {
                // defaulted to system and user timezone
                $system_timezone_offset = Carbon::now(config("app.timezone"))->getOffsetString();
                $user_timezone_offset = Carbon::now(Auth::user()->timezone ?? config("app.timezone"))->getOffsetString();
                $query->whereRaw("DATE_FORMAT(CONVERT_TZ(created_at, '$system_timezone_offset', '$user_timezone_offset'), " . DateTimeFormatterService::DBDateTimeFormatter("created_at", $user_timezone_offset) . ")  like ?", ["%$keyword%"]);
            })
            ->filterColumn("status", function ($query, $keyword) {
                $values = [
                    0 => __("general.message.failed"),
                    1 => __("general.message.success"),
                ];
                $sql = "CASE";
                foreach ($values as $key => $value) {
                    $sql .= " WHEN status = $key THEN '$value'";
                }
                $sql .= " END like ?";
                $query->whereRaw($sql, ["%$keyword%"]);
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param  BackupLog  $model
     *
     * @return Builder
     */
    public function query(BackupLog $model): Builder
    {
        $request = $this->request();
        return $model->newQuery()
            ->where(function ($query) use ($request) {
                if ($request->get("start_date") != "" && $request->get("end_date") != "") {
                    $query->whereBetween("backup_logs.created_at", [
                        Carbon::createFromFormat("Y-m-d", $request->get("start_date"))->setTimezone(Auth::user()->timezone ?? config("app.timezone"))->setTime("00", "00", "00", "000000")->setTimezone(config("app.timezone")),
                        Carbon::createFromFormat("Y-m-d", $request->get("end_date"))->setTimezone(Auth::user()->timezone ?? config("app.timezone"))->setTime("23", "59", "59", "999999")->setTimezone(config("app.timezone"))
                    ]);
                }
            });
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): \Yajra\DataTables\Html\Builder
    {
        $parameters = [
            "start_date" => "$('#startDate').val()",
            "end_date" => "$('#endDate').val()"
        ];

        return $this->builder()
            ->setTableId(self::TABLE_NAME)
            ->columns($this->getColumns())
            ->pageLength(50)
            ->minifiedAjax("", null, $parameters)
            ->stateSave(false)
            ->responsive()
            ->autoWidth(false)
            ->parameters([
                "order" => [
                    [1, "desc"],
                ],
                "initComplete" => "function() {
                    $('#" . $this->builder()->getTableId() . "').bind('DOMNodeInserted', function(e) {
                        KTMenu.createInstances();
                    });
                }",
                "drawCallback" => "function() { KTMenu.createInstances();}",
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
            Column::make("event")
                ->title(__("backup_log.log.table_header.event"))
                ->width("15%")
                ->responsivePriority(-1),
            Column::make("created_at")
                ->title(__("backup_log.log.table_header.date_and_time"))
                ->width("20%"),
            Column::make("disk_name")
                ->title(__("backup_log.log.table_header.disk_name"))
                ->width("10%"),
            Column::make("backup_name")
                ->title(__("backup_log.log.table_header.backup_name"))
                ->width("25%"),
            Column::make("message")
                ->title(__("backup_log.log.table_header.message"))
                ->width("25%"),
            Column::make("status")
                ->title(__("backup_log.log.table_header.status"))
                ->width("10%"),
            Column::computed("action")
                ->title(__("backup_log.log.table_header.action"))
                ->width("10%")
                ->responsivePriority(-1),
            Column::computed("stack_trace")
                ->title(__("backup_log.log.table_header.stack_trace"))
                ->addClass("none"),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return "BackupLogs_".date("YmdHis");
    }
}
