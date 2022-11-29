<?php

namespace App\DataTables\Logs;

use App\Services\DateTimeFormatterService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AuditLogsDataTable extends DataTable
{
    public const TABLE_NAME = "audit_log_table";

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
            ->rawColumns(["description", "properties", "action"])
            ->addColumn("action", function ($model) {
                $actions = array(
                    "delete" => [
                        "url" => route("system.log.audit.destroy", $model->id),
                        "label" => __("general.button.delete"),
                        "disabled" => Auth::user()->cannotDelete(Activity::class)
                    ]
                );

                $forceDropdown = true;

                return view("pages.common-components.buttons.action-menu-button", compact("actions", "forceDropdown"));
            })
            ->editColumn("causer_id", function (Activity $model) {
                return $model->causer ? ($model->causer->name ?? __("System")) : __("System");
            })
            ->editColumn("created_at", function (Activity $model) {
                return DateTimeFormatterService::formatModalDateTime($model->created_at);
            })
            ->editColumn("description", function (Activity $model) {
                return __(explode(":", $model->description)[0]);
            })
            ->editColumn("subject_type", function (Activity $model) {
                $subject_type = explode("\\", $model->subject_type);
                return __("audit_log.module." . end($subject_type));
            })
            ->editColumn("properties", function (Activity $model) {
                $content = $model->properties;

                return view("pages.log.audit._details", compact("content"));
            })
            ->filterColumn("description", function (Builder $query, $keyword) {
                $messages = __("audit_log.message");
                $description_raw_query = "CASE SUBSTRING_INDEX(activity_log.description, ':', 1)" . collect($messages)->map(function ($string, $module) {
                        return " WHEN '$module' THEN '$string' ";
                    })->join("") . "ELSE SUBSTRING_INDEX(activity_log.description, ':', 1) END";
                $query->whereRaw("$description_raw_query LIKE ?", ["%{$keyword}%"]);
            })
            ->filterColumn("subject_name", function (Builder $query, $keyword) {
                $modules = Activity::query()
                    ->distinct("subject_type")
                    ->pluck("subject_type");

                $subject_name_query = "CASE";
                foreach ($modules as $module) {
                    if (Schema::hasColumn(app($module)->getTable(), "name")) {
                        $subject_name_query .= " WHEN activity_log.subject_type = '" . str_replace("\\", "\\\\", $module) . "' THEN " . app($module)->getTable() . ".name";
                    } else if(Schema::hasColumn(app($module)->getTable(), "title")) {
                        $subject_name_query .= " WHEN activity_log.subject_type = '" . str_replace("\\", "\\\\", $module) . "' THEN " . app($module)->getTable() . ".title";
                    } else if (Schema::hasColumn(app($module)->getTable(), "endpoint")) {
                        $subject_name_query .= " WHEN activity_log.subject_type = '" . str_replace("\\", "\\\\", $module) . "' THEN " . app($module)->getTable() . ".endpoint";
                    }
                }
                $subject_name_query .= " END";
                $query->whereRaw("CASE WHEN " . $subject_name_query . " IS NULL THEN SUBSTRING_INDEX(activity_log.description, ':', (0 - (length(activity_log.description) - length(replace(activity_log.description, ':', ''))))) ELSE $subject_name_query END like ?", ["%{$keyword}%"]);
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param  Activity  $model
     *
     * @return Builder
     */
    public function query(Activity $model): Builder
    {
        $request = $this->request();
        $query = $model->newQuery()
            ->where(function ($query) use ($request) {
                if($request->get("start_date") != "" && $request->get("end_date") != "") {
                    $query->whereBetween("activity_log.created_at", [
                        Carbon::createFromFormat("Y-m-d", $request->get("start_date"))->setTimezone(Auth::user()->timezone ?? config("app.timezone"))->setTime("00", "00", "00", "000000")->setTimezone(config("app.timezone")),
                        Carbon::createFromFormat("Y-m-d", $request->get("end_date"))->setTimezone(Auth::user()->timezone ?? config("app.timezone"))->setTime("23", "59", "59", "999999")->setTimezone(config("app.timezone"))
                    ]);
                }
                if($request->get("filter_module") != "") {
                    $query->where("activity_log.subject_type", $request->get("filter_module"));
                }
            });

        $modules = Activity::query()
            ->distinct("subject_type")
            ->pluck("subject_type");

        $subject_name_query = "''";
        if ($modules->count() > 0) {
            $subject_name_query = "CASE";
            foreach ($modules as $module) {
                $query->leftJoin(app($module)->getTable(), function ($join) use ($module) {
                    $join->on(app($module)->getTable() . ".id", "=", "activity_log.subject_id");
                });

                if (Schema::hasColumn(app($module)->getTable(), "name")) {
                    $subject_name_query .= " WHEN activity_log.subject_type = '" . str_replace("\\", "\\\\", $module) . "' THEN " . app($module)->getTable() . ".name";
                } else if (Schema::hasColumn(app($module)->getTable(), "title")) {
                    $subject_name_query .= " WHEN activity_log.subject_type = '" . str_replace("\\", "\\\\", $module) . "' THEN " . app($module)->getTable() . ".title";
                } else if (Schema::hasColumn(app($module)->getTable(), "endpoint")) {
                    $subject_name_query .= " WHEN activity_log.subject_type = '" . str_replace("\\", "\\\\", $module) . "' THEN " . app($module)->getTable() . ".endpoint";
                }
            }
            $subject_name_query .= " END";
        }

        $query->select(
            "activity_log.*",
            DB::raw("CASE WHEN " . $subject_name_query . " IS NULL THEN SUBSTRING_INDEX(activity_log.description, ':', (0 - (length(activity_log.description) - length(replace(activity_log.description, ':', ''))))) ELSE $subject_name_query END AS subject_name")
        );

        return $query;
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
            "end_date" => "$('#endDate').val()",
            "filter_module" => "$('#filterModule').val()"
        ];

        return $this->builder()
            ->setTableId(self::TABLE_NAME)
            ->columns($this->getColumns())
            ->pageLength(50)
            ->minifiedAjax("", null, $parameters)
            ->stateSave(false)
            ->orderBy(1)
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
            Column::make("causer_id")
                ->title(__("audit_log.table_header.user"))
                ->addClass("ps-0")
                ->responsivePriority(-1),
            Column::make("created_at")
                ->title(__("audit_log.table_header.date_and_time"))
                ->searchable(false),
            Column::make("description")
                ->title(__("audit_log.table_header.activity")),
            Column::make("subject_type")
                ->title(__("audit_log.table_header.module"))
                ->searchable(false),
            Column::make("subject_name")
                ->title(__("audit_log.table_header.subject")),
            Column::computed("action")
                ->title(__("audit_log.table_header.action"))
                ->responsivePriority(-1),
            Column::computed("properties")
                ->title(__("audit_log.table_header.properties"))
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
        return "DataLogs_".date("YmdHis");
    }
}
