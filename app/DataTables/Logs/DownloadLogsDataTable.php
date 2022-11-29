<?php

namespace App\DataTables\Logs;

use App\Models\Download;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use function datatables;

class DownloadLogsDataTable extends DataTable
{
    public const TABLE_NAME = "download_log_table";

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
            ->editColumn("downloadable_type", function (Download $model) {
                return __("download_log.module." . str_replace("App\\Models\\", "", $model->downloadable_type));
            })
            ->filterColumn("downloadable_name", function (Builder $query, $keyword) {
                $modules = Download::query()
                    ->distinct("downloadable_type")
                    ->pluck("downloadable_type");

                $downloadable_name_query = "CASE";
                foreach ($modules as $module) {
                    $query->leftJoin(app($module)->getTable(), function($join) use ($module) {
                        $join->on(app($module)->getTable() . ".id", "=", "downloads.downloadable_id");
                    });

                    if (Schema::hasColumn(app($module)->getTable(), "name")) {
                        $downloadable_name_query .= " WHEN downloads.downloadable_type = '" . str_replace("\\", "\\\\", $module) . "' THEN " . app($module)->getTable() . ".name";
                    } else if(Schema::hasColumn(app($module)->getTable(), "title")) {
                        $downloadable_name_query .= " WHEN downloads.downloadable_type = '" . str_replace("\\", "\\\\", $module) . "' THEN " . app($module)->getTable() . ".title";
                    }
                }
                $downloadable_name_query .= " END";
                $query->whereRaw("$downloadable_name_query like ?", ["%{$keyword}%"]);
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Download $model
     *
     * @return Builder
     */
    public function query(Download $model): Builder
    {
        $query = $model->newQuery();

        $modules = Download::query()
            ->distinct("downloadable_type")
            ->pluck("downloadable_type");

        $downloadable_name_query = "CASE";
        foreach ($modules as $module) {
            $query->leftJoin(app($module)->getTable(), function($join) use ($module) {
                $join->on(app($module)->getTable() . ".id", "=", "downloads.downloadable_id");
            });

            if (Schema::hasColumn(app($module)->getTable(), "name")) {
                $downloadable_name_query .= " WHEN downloads.downloadable_type = '" . str_replace("\\", "\\\\", $module) . "' THEN " . app($module)->getTable() . ".name";
            } else if(Schema::hasColumn(app($module)->getTable(), "title")) {
                $downloadable_name_query .= " WHEN downloads.downloadable_type = '" . str_replace("\\", "\\\\", $module) . "' THEN " . app($module)->getTable() . ".title";
            }
        }
        $downloadable_name_query .= " END";
        return $query->select(
            "downloadable_type",
            DB::raw("$downloadable_name_query AS downloadable_name"),
            DB::raw("COUNT(downloads.id) AS total")
        )->groupBy("downloadable_type", "downloadable_name");
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
            Column::make("downloadable_type")
                ->title(__("download_log.table_header.module"))
                ->width("25%"),
            Column::make("downloadable_name")
                ->title(__("download_log.table_header.subject"))
                ->width("55%"),
            Column::make("total")
                ->title(__("download_log.table_header.total"))
                ->width("20%")
                ->searchable(false),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return "DownloadLogs_" . date("YmdHis");
    }
}
