<?php

namespace App\DataTables\Logs;

use App\Services\DateTimeFormatterService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FailedJobLogsDataTable extends DataTable
{
    public const TABLE_NAME = "failedJobLog_table";

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     *
     * @return DataTableAbstract
     * @throws Exception
     */
    public function dataTable(mixed $query): DataTableAbstract
    {
        return datatables()
            ->of($query)
            ->rawColumns(["failed_at", "action", "payload", "stack_trace", 'exception'])
            ->addColumn('job', function ($job) {
                $job_arr = json_decode($job->payload);
                return (isset($job_arr->displayName)) ? str_replace('App\Jobs\\', "", $job_arr->displayName) : __("general.message.not_applicable");
            })
            ->addColumn("action", function ($model) {
                $actions = array(
                    "retry" => array(
                        "url" => route("system.log.failed_job.retry", $model->id),
                        "label" => __("failed_job_log.button.retry"),
                        "disabled" => Auth::user()->cannotRetry('FailedJobLog')
                    ),
                    "delete" => [
                        "url" => route("system.log.failed_job.destroy", $model->id),
                        "label" => __("general.button.delete"),
                        "disabled" => Auth::user()->cannotDelete('FailedJobLog')
                    ]
                );

                $forceDropdown = true;

                return view("pages.common-components.buttons.action-menu-button", compact("actions", "forceDropdown"));
            })
            ->editColumn('exception', function ($job) {
                $exc = explode("Stack trace:", $job->exception, 2);
                $exc =  (isset($exc[0])) ? explode("\n", $exc[0], 2) : $exc;

                $id = $job->id;
                $heading = (isset($exc[0])) ? nl2br(trim($exc[0])) : "";
                $content = (isset($exc[1])) ? nl2br(trim($exc[1])) : "";

                if ($content != "") {
                    return view("pages.log.failed_job._exception", compact("content", "heading", "id"));
                }

                return $content;
            })
            ->addColumn('stack_trace', function ($job) {
                $exc = explode("Stack trace:", $job->exception, 2);

                $content = (isset($exc[1])) ? trim($exc[1]) : trim($job->exception);

                if ($content != "") {
                    return view("pages.log.failed_job._details", compact("content"));
                }

                return $content;
            })
            ->editColumn("failed_at", function ($model) {
                return DateTimeFormatterService::formatModalDateTime($model->failed_at);
            })
            ->editColumn("payload", function ($model) {
                $content = $model->payload;

                if($content != "") {
                    return view("pages.log.failed_job._details", compact("content"));
                }

                return $content;
            })
            ->filterColumn("failed_at", function ($query, $keyword) {
                // defaulted to system and user timezone
                $system_timezone_offset = Carbon::now(config("app.timezone"))->getOffsetString();
                $user_timezone_offset = Carbon::now(Auth::user()->timezone ?? config("app.timezone"))->getOffsetString();
                $query->whereRaw("DATE_FORMAT(CONVERT_TZ(failed_at, '$system_timezone_offset', '$user_timezone_offset'), " . DateTimeFormatterService::DBDateTimeFormatter("created_at", $user_timezone_offset) . ")  like ?", ["%$keyword%"]);
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        $request = $this->request();
        return DB::table('failed_jobs')
            ->where(function ($query) use ($request) {
                if ($request->get("start_date") != "" && $request->get("end_date") != "") {
                    $query->whereBetween("failed_jobs.failed_at", [
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

        // for parameters that is not used for filtering
        $non_filter_parameters = [];

        // for parameters that is used for filtering - retrieve from different between non-filter parameters and all parameters
        $filter_parameters = json_encode(array_diff(array_keys($parameters), $non_filter_parameters));

        $view_more_text = __("failed_job_log.button.view_more");
        $view_less_text = __("failed_job_log.button.view_less");
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
                    [2, "desc"],
                ],
                "initComplete" => "function() {
                    $('#" . $this->builder()->getTableId() . "').bind('DOMNodeInserted', function(e) {
                        KTMenu.createInstances();
                        const viewStack = $('.viewStack');
                        viewStack.unbind('click');
                        viewStack.click(function(t) {
                            t.stopPropagation();
                            t.preventDefault();
                            $(this).blur();
                            $('.'+$(this).data('display')).toggle();
                            if('" . $view_more_text . "'=== $(this).find('span').text()) {
                                $(this).find('span').text('" . $view_less_text . "');
                            } else {
                                $(this).find('span').text('" . $view_more_text . "');
                            }
                        });
                    });
                }",
                "drawCallback" => "function() {
                    KTMenu.createInstances();
                        const viewStack = $('.viewStack');
                        viewStack.unbind('click');
                        viewStack.click(function(t) {
                            t.stopPropagation();
                            t.preventDefault();
                            $(this).blur();
                            $('.'+$(this).data('display')).toggle();
                            if('" . $view_more_text . "'=== $(this).find('span').text()) {
                                $(this).find('span').text('" . $view_less_text . "');
                            } else {
                                $(this).find('span').text('" . $view_more_text . "');
                            }
                        });
                    initDataTableEmptyState('" . $this->builder()->getTableId() . "', this.api(), JSON.parse('" . $filter_parameters . "'));
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
            Column::make("job")
                ->title(__("failed_job_log.table_header.job"))
                ->width("15%"),
            Column::make("queue")
                ->title(__("failed_job_log.table_header.queue"))
                ->width("10%"),
            Column::make("failed_at")
                ->title(__("failed_job_log.table_header.failed_at"))
                ->width("20%"),
            Column::make("exception")
                ->title(__("failed_job_log.table_header.exception"))
                ->width("30%"),
            Column::computed("action")
                ->title(__("failed_job_log.table_header.action"))
                ->width("10%"),
            Column::computed("stack_trace")
                ->title(__("failed_job_log.table_header.stack_trace"))
                ->addClass("none"),
            Column::computed("payload")
                ->title(__("failed_job_log.table_header.payload"))
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
        return "FailedJobLogs_".date("YmdHis");
    }
}
