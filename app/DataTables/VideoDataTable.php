<?php

namespace App\DataTables;

use App\Models\Download;
use App\Models\Video;
use App\Services\DataTableRenderHelper;
use App\Services\DateTimeFormatterService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class VideoDataTable extends DataTable
{
    public const TABLE_NAME = "video_table";

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
            ->rawColumns(["order", "action","title", "thumbnail_path", "size", "download", "status", "updated_at"])
            ->editColumn("order", function (Video $model) {
                return view("pages.common-components.buttons.move-item-button");
            })
            ->addColumn("action", function (Video $model) {
                $actions = [
                    "edit" => [
                        "url" => route("video.edit", ["video" => $model->id]),
                        "label" => __("general.button.edit"),
                        "disabled" => Auth::user()->cannotUpdate(Video::class)
                    ],
                    "delete" => [
                        "url" => route("video.destroy", ["video" => $model->id]),
                        "label" => __("general.button.delete"),
                        "disabled" => Auth::user()->cannotDelete(Video::class)
                    ]
                ];

                $forceDropdown = true;

                return view("pages.common-components.buttons.action-menu-button", compact("actions", "forceDropdown"));
            })
            ->editColumn("title", function (Video $model) {
                $ytbadge = '<span data-bs-toggle="tooltip" title="" data-bs-original-title="'. __("video.message.youtube_video") . '"><!--begin::Svg Icon | path: media/icons/duotune/social/soc007.svg-->
                            <span class="svg-icon svg-icon-primary svg-icon-1 svg-icon-danger me-1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M21 6.30005C20.5 5.30005 19.9 5.19998 18.7 5.09998C17.5 4.99998 14.5 5 11.9 5C9.29999 5 6.29998 4.99998 5.09998 5.09998C3.89998 5.19998 3.29999 5.30005 2.79999 6.30005C2.19999 7.30005 2 8.90002 2 11.9C2 14.8 2.29999 16.5 2.79999 17.5C3.29999 18.5 3.89998 18.6001 5.09998 18.7001C6.29998 18.8001 9.29999 18.8 11.9 18.8C14.5 18.8 17.5 18.8001 18.7 18.7001C19.9 18.6001 20.5 18.4 21 17.5C21.6 16.5 21.8 14.9 21.8 11.9C21.8 9.00002 21.5 7.30005 21 6.30005ZM9.89999 15.7001V8.20007L14.5 11C15.3 11.5 15.3 12.5 14.5 13L9.89999 15.7001Z" fill="#FF0000"></path>
                            </svg></span>
                            <!--end::Svg Icon--></span>';
                return DataTableRenderHelper::renderMultipleFields($model, [
                    "title" => fn ($title) => ($model->video_type == "youtube" ? $ytbadge: "") . DataTableRenderHelper::renderTitle($model, route("video.edit", ["video" => $model->id])),
                    "duration" => fn ($duration, $file_size) => '<span class="text-muted">'. $duration .' | '. $file_size->file_size .'</span>',
                ]);
            })
            ->editColumn("thumbnail_path", function (Video $model){
                return DataTableRenderHelper::renderVideo($model, $model->video_type == "youtube" ? $model->youtube_url : "file_path", "thumbnail_path", $model->video_type == "youtube");
            })
            ->editColumn("file_size", function(Video $model){
                return $model->file_size ?? __("general.message.not_applicable");
            })
            ->editColumn("updated_at", function (Video $model) {
                return DataTableRenderHelper::renderDateTime($model, "updated_at");
            })
            ->editColumn("status", function (Video $model) {
                return DataTableRenderHelper::renderBadge($model, "status", [
                    0 => "danger",
                    1 => "success",
                ], [
                    0 => __("general.message.inactive"),
                    1 => __("general.message.active"),
                ]);
            })
            ->filterColumn("updated_at", DataTableRenderHelper::filterDateTime())
            ->filterColumn("status", DataTableRenderHelper::filterStatus(
                values: [
                    0 => __("general.message.inactive"),
                    1 => __("general.message.active"),
                ]
            ));

    }

    /**
     * Get query source of dataTable.
     *
     * @param  Video  $model
     *
     * @return Builder
     */
    public function query(Video $model): Builder
    {
        $request = $this->request();

        $downloads = Download::query()->select("downloadable_id", DB::raw("COUNT(id) AS total_downloads"))->where("downloadable_type", "=", Video::class)->groupBy("downloadable_id");
        return $model->newQuery()
            ->leftJoinSub($downloads, "downloads", function ($join) {
                $join->on("videos.id", "=", "downloads.downloadable_id");
            })
            ->where(function ($query) use ($request) {
                if(in_array(($request->get("filter_video_type") ?? ""), ["upload", "youtube"])) {
                    $query->where("videos.video_type", "=", $request->get("filter_video_type"));
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
            "filter_video_type" => "$('#filterVideoType').val()",
        ];

        // for parameters that is not used for filtering
        $non_filter_parameters = [];

        // for parameters that is used for filtering - retrieve from different between non-filter parameters and all parameters
        $filter_parameters = json_encode(array_diff(array_keys($parameters), $non_filter_parameters));

        return $this->builder()
            ->setTableId(self::TABLE_NAME)
            ->columns($this->getColumns())
            ->orders([0, "asc"])
            ->pageLength(50)
            ->lengthMenu([
                [10, 25, 50, 100, -1],
                ['10', '25', '50', '100', __("general.message.all")]
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
                        window.location.href = '" . route("video.edit", ["video" => ":id"]) . "'.replace(':id', data.id);
                    })
                }",*/
//                "rowCallback" => "function (row, data) {
//                    $(row).find('td:nth-child(2)').attr('align', 'center');
//                }",
                "rowCallback" => "function (row, data) {
                    $(row).attr('data-id', data.id);
                }",
                "initComplete" => "function() {
                    initDataTableRowRearrange('" . $this->builder()->getTableId() . "', this.api(), '" . route("video.rearrange") . "');
                    $('#" . $this->builder()->getTableId() . "').bind('DOMNodeInserted', function(e) {
                        KTMenu.createInstances();
                    });
                }",
                "drawCallback" => "function() {
                    KTMenu.createInstances();
                    refreshFsLightbox();
                    initDataTableEmptyState('" . $this->builder()->getTableId() . "', this.api(), JSON.parse('" . $filter_parameters . "'));
                    reinitDataTableTooltips('" . $this->builder()->getTableId() . "');
                    refreshDataTableRowRearrange('" . $this->builder()->getTableId() . "', this.api());
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
            Column::make("order")
                ->title(__("video.table_header.arrange"))
                ->addClass("align-top-th no-blur")
                ->responsivePriority(-1)
                ->visible(false)
                ->searchable(false)
                ->width("8%"),
            Column::computed("thumbnail_path")
                ->title(__("video.table_header.video"))
                ->addClass("align-top no-blur")
                ->width("15%")
                ->responsivePriority(-1),
            Column::make("title")
                ->title(__("video.table_header.title"))
                ->addClass("align-top no-blur")
                ->width("43%"),
            Column::make("total_downloads")
                ->title(__("video.table_header.total_download"))
                ->addClass("align-top")
                ->width("12%")
                ->searchable(false),
            Column::make("updated_at")
                ->title(__("video.table_header.last_update"))
                ->addClass("align-top")
                ->width("10%"),
            Column::computed("status")
                ->title(__("video.table_header.status"))
                ->addClass("align-top")
                ->width("10%"),
            Column::computed("action")
                ->title(__("video.table_header.action"))
                ->addClass("align-top")
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
        return "Videos_".date("YmdHis");
    }
}
