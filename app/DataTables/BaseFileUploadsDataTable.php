<?php

namespace App\DataTables;

use App\Models\Download;
use App\Services\DataTableRenderHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BaseFileUploadsDataTable extends DataTable
{
    protected string $model;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->model = Str::replaceFirst("App\DataTables\\", "", Str::singular(Str::replaceLast("DataTable","", get_called_class())));
    }

    protected function getBaseModel()
    {
        return app("App\\Models\\" . $this->model);
    }

    protected function getBaseModelName(): string
    {
        return strtolower($this->model);
    }

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
            ->rawColumns(["order", "action", "title", "thumbnail_path", "updated_at", "status"])
            ->editColumn("order", function ($model) {
                return view("pages.common-components.buttons.move-item-button");
            })
            ->addColumn("action", function ($model) {
                $actions = [
                    "edit" => [
                        "url" => route($this->getBaseModelName() . ".edit", [$this->getBaseModelName() => $model->id]),
                        "label" => __("general.button.edit"),
                        "disabled" => Auth::user()->cannotUpdate($this->getBaseModel()::class)
                    ],
                    "delete" => [
                        "url" => route($this->getBaseModelName() . ".destroy", [$this->getBaseModelName() => $model->id]),
                        "label" => __("general.button.delete"),
                        "disabled" => Auth::user()->cannotDelete($this->getBaseModel()::class)
                    ]
                ];

                $forceDropdown = true;

                return view("pages.common-components.buttons.action-menu-button", compact("actions", "forceDropdown"));
            })
            ->editColumn("title", function ($model) {
                return DataTableRenderHelper::renderMultipleFields($model, [
                    "title" => fn ($title) => DataTableRenderHelper::renderTitle($model, route($this->getBaseModelName() . ".edit", [$this->getBaseModelName() => $model->id])),
                    "file_size" => fn ($file_size) => '<span class="text-muted">'. $file_size .'</span>',
                ]);
            })
            ->editColumn("thumbnail_path", function ($model) {
                return DataTableRenderHelper::renderPdf($model, "file_path", "thumbnail_path");
            })
            ->editColumn("updated_at", function ($model) {
                return DataTableRenderHelper::renderDateTime($model);
            })
            ->editColumn("status", function ($model) {
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
     * @return Builder
     */
    public function query(): Builder
    {
        $downloads = Download::query()->select("downloadable_id", DB::raw("COUNT(id) AS total_downloads"))->where("downloadable_type", "=", $this->getBaseModel()::class)->groupBy("downloadable_id");
        return $this->getBaseModel()->newQuery()->leftJoinSub($downloads, "downloads", function ($join) {
            $join->on((new ($this->getBaseModel()))->getTable() . ".id", "=", "downloads.downloadable_id");
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
            ->setTableId($this->getBaseModelName() . "_table")
            ->columns($this->getColumns())
            ->orders([0, "asc"])
            ->pageLength(50)
            ->lengthMenu([
                [10, 25, 50, 100, -1],
                ["10", "25", "50", "100", __("general.message.all")]
            ])
            ->minifiedAjax()
            ->stateSave(false)
            // ->orderBy(1)
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
                        window.location.href = '" . route($this->getBaseModelName() . ".edit", [$this->getBaseModelName() => ":id"]) . "'.replace(':id', data.id);
                    })
                }",*/
                "rowCallback" => "function (row, data) {
                    $(row).attr('data-id', data.id);
                }",
                "initComplete" => "function() {
                    initDataTableRowRearrange('" . $this->builder()->getTableId() . "', this.api(), '" . route($this->getBaseModelName().".rearrange") . "');
                    $('#" . $this->builder()->getTableId() . "').bind('DOMNodeInserted', function(e) {
                        KTMenu.createInstances();
                    });
                }",
                "drawCallback" => "function() {
                    KTMenu.createInstances();
                    refreshFsLightbox();
                    refreshDataTableRowRearrange('" . $this->builder()->getTableId() . "', this.api());

                    // Toggle custom lightbox source to show PDF
                    $('.view-pdf').on('click', function () {
                        $('.fslightbox-source').attr('src', $(this).attr('data-pdf-link')).show();
                    });

                    initDataTableEmptyState('". $this->builder()->getTableId() . "', this.api());
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
                ->title(__($this->getBaseModelName() . ".table_header.arrange"))
                ->addClass("align-top-th no-blur")
                ->responsivePriority(-1)
                ->visible(false)
                ->searchable(false)
                ->width("8%"),
            Column::computed("thumbnail_path")
                ->title(__($this->getBaseModelName() . ".table_header." . $this->getBaseModelName()))
                ->addClass("align-top no-blur")
                ->width("10%")
                ->responsivePriority(-1),
            Column::make("title")
                ->title(__($this->getBaseModelName() . ".table_header.title"))
                ->addClass("align-top no-blur")
                ->width("48%"),
            Column::make("total_downloads")
                ->title(__($this->getBaseModelName() . ".table_header.total_download"))
                ->addClass("align-top")
                ->width("12%")
                ->searchable(false),
            Column::make("updated_at")
                ->title(__($this->getBaseModelName() . ".table_header.last_updated"))
                ->addClass("align-top")
                ->width("10%"),
            Column::make("status")
                ->title(__($this->getBaseModelName() . ".table_header.status"))
                ->addClass("align-top")
                ->width("10%"),
            Column::computed("action")
                ->title(__($this->getBaseModelName() . ".table_header.action"))
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
        return $this->model . "_" . date("YmdHis");
    }
}
