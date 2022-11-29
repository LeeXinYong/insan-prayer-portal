<?php

namespace App\DataTables;

use App\Models\Banner;
use App\Models\Download;
use App\Services\DataTableRenderHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BannersDataTable extends DataTable
{
    public const TABLE_NAME = "banner_table";

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
            ->rawColumns(["order", "action", "title", "banner_path", "url", "updated_at", "status"])
            ->editColumn("order", function (Banner $model) {
                return view("pages.common-components.buttons.move-item-button");
            })
            ->addColumn("action", function (Banner $model) {
                $actions = [
                    "edit" => [
                        "url" => route("banner.edit", ["banner" => $model->id]),
                        "label" => __("general.button.edit"),
                        "disabled" => Auth::user()->cannotUpdate(Banner::class)
                    ],
                    "delete" => [
                        "url" => route("banner.destroy", ["banner" => $model->id]),
                        "label" => __("general.button.delete"),
                        "disabled" => Auth::user()->cannotDelete(Banner::class)
                    ]
                ];

                $forceDropdown = true;

                return view("pages.common-components.buttons.action-menu-button", compact("actions", "forceDropdown"));
            })
            ->editColumn("banner_path", function (Banner $model) {
                return DataTableRenderHelper::renderImage($model, "banner_path");
            })
            ->editColumn("title", function ($model) {
                return DataTableRenderHelper::renderMultipleFields($model, [
                    "title" => fn ($title) => DataTableRenderHelper::renderTitle($model, route("banner.edit", ["banner" => $model->id])),
                    "_" => fn ($_, $model) => ($model->url_content_flag == "url") ? DataTableRenderHelper::renderUrl($model) : DataTableRenderHelper::renderMobileView($model),
                ]);
            })
            ->editColumn("updated_at", function (Banner $model) {
                return DataTableRenderHelper::renderDateTime($model);
            })
            ->editColumn("status", function (Banner $model) {
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
     * @param  Banner  $model
     *
     * @return Builder
     */
    public function query(Banner $model): Builder
    {
        $views = Download::query()->select("downloadable_id", DB::raw("COUNT(id) AS total_views"))->where("downloadable_type", "=", Banner::class)->groupBy("downloadable_id");
        return $model->newQuery()->leftJoinSub($views, "views", function ($join) {
            $join->on("banners.id", "=", "views.downloadable_id");
        });
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
            ->orders([0, "asc"])
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
                */
                "rowCallback" => "function (row, data) {
                    $(row).attr('data-id', data.id);
                }",
                "initComplete" => "function() {
                    initDataTableRowRearrange('" . $this->builder()->getTableId() . "', this.api(), '" . route("banner.rearrange") . "');
                    $('#" . $this->builder()->getTableId() . "').bind('DOMNodeInserted', function(e) {
                        KTMenu.createInstances();
                    });
                }",
                "drawCallback" => "function() {
                    KTMenu.createInstances();
                    refreshFsLightbox();
                    refreshDataTableRowRearrange('" . $this->builder()->getTableId() . "', this.api());

                    $('.view-content').on('click', function () {
                        const data = window." . config("datatables-html.namespace", "LaravelDataTables") . "['" . $this->builder()->getTableId() . "'].row($(this).closest('tr')).data();
                        const title = data['title'].split('<br/>')[0];
                        const content = document.createElement('textarea');
                        content.innerHTML = data['content'];

                        // set contents to iframe
                        const iframe = document.getElementById('mobile_preview_content');
                        const iframedoc = iframe.contentDocument || iframe.contentWindow.document;

                        // Put the content in the iframe
                        iframedoc.open();
                        iframedoc.writeln(`
                            <div class='container d-flex flex-column mb-25' style='text-align: center'>
                                <h1 class='my-10 font-weight-700 narrower' style='word-break: break-word; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font-size: calc(1.3rem + 0.6vw); margin-top: 5px; margin-bottom: 5px;'>
                                    ` + title + `
                                </h1>
                                <div style='text-align: left'>
                                    <div class='pb-5 narrower' id='resource-content'>`+unescape(content.value)+`</div>
                                </div>
                            </div>
                        `);
                        iframedoc.close();

                        $('#mobile_preview_modal').modal('show');
                    });

                    initDataTableEmptyState('" . $this->builder()->getTableId() . "', this.api());
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
                ->title(__("banner.table_header.arrange"))
                ->addClass("align-top-th no-blur")
                ->responsivePriority(-1)
                ->visible(false)
                ->searchable(false)
                ->width("8%"),
            Column::computed("banner_path")
                ->title(__("banner.table_header.banner"))
                ->addClass("align-top no-blur")
                ->width("10%")
                ->responsivePriority(-1),
            Column::make("title")
                ->title(__("banner.table_header.title"))
                ->addClass("align-top no-blur")
                ->width("50%"),
            Column::make("total_views")
                ->title(__("banner.table_header.total_view"))
                ->addClass("align-top")
                ->width("10%")
                ->searchable(false),
            Column::make("updated_at")
                ->title(__("banner.table_header.last_updated"))
                ->addClass("align-top")
                ->width("10%"),
            Column::make("status")
                ->title(__("banner.table_header.status"))
                ->addClass("align-top")
                ->width("10%"),
            Column::computed("action")
                ->title(__("banner.table_header.action"))
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
        return "Banners_".date("YmdHis");
    }
}
