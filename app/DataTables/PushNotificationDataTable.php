<?php

namespace App\DataTables;

use App\Http\Resources\VideoResource;
use App\Models\Enums\PushNotificationAction;
use App\Models\PushNotification;
use App\Models\Video;
use App\Services\DataTableRenderHelper;
use App\Services\PushNotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PushNotificationDataTable extends DataTable
{
    const TABLE_NAME = "push_notifications_table";

    private bool $isImageEnabled;
    private bool $isLargeIconEnabled;

    public function __construct()
    {
        $service = App::make(PushNotificationService::class);
        $this->isImageEnabled = $service->isImageEnabled();
        $this->isLargeIconEnabled = $service->isLargeIconEnabled();
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     */
    public function dataTable(mixed $query): DataTableAbstract
    {
        $datatable = datatables()
            ->eloquent($query)
            ->addColumn("notification", function (PushNotification $model) {
                $columns = [
                    "title" => fn($title) => "<span class='fw-bold'>" . $title . "</span>",
                    "body" => fn($body) => "<span class='text-muted fs-7'>" . $body . "</span>",
                ];
                return DataTableRenderHelper::renderMultipleFields($model, $columns);
            })
            ->editColumn("action", function (PushNotification $model) {
                $pushNotificationAction = PushNotificationAction::guess($model->action["class"]);
                $action = "<span class='fw-bold'>" . __("notification.create.actions." . $pushNotificationAction->name) . "</span>";

                $targetItem = $model->action["item"];
                $target = match ($pushNotificationAction) {
                    PushNotificationAction::Default => null,
                    PushNotificationAction::Video => "<div class='d-flex flex-start'><i class='fa fa-film fs-4 me-1'></i>" . VideoResource::make(Video::query()->find($targetItem))?->title . "</div>",
                };
                if ($target) {
                    $action .= "<br><span class='text-muted fs-7'>" . $target . "</span>";
                }
                return $action;
            })
            ->orderColumn("notification", "title $1")
            ->filterColumn("notification", function ($query, $keyword) {
                $keyword = strtolower($keyword);
                $query->where(function ($query) use ($keyword) {
                    $query->whereRaw("LOWER(title) like ?", "%{$keyword}%")
                        ->orWhereRaw("LOWER(body) like ?", "%{$keyword}%");
                });
            })
            ->filterColumn("action", function ($query, $keyword) {
                $keyword = strtolower($keyword);
                $query->where(function ($query) use ($keyword) {
                    $query->whereRaw("LOWER(JSON_EXTRACT(`push_notifications`.`action`, '$.class')) like ?", "%{$keyword}%")
                        ->orWhereRaw("(
                            CASE
                                WHEN JSON_EXTRACT(`push_notifications`.`action`, '$.class') = 'Promotion' THEN (select LOWER(`promotions`.`title`) from promotions where CAST(id AS CHAR) = JSON_EXTRACT(`push_notifications`.`action`, '$.item'))
                                WHEN JSON_EXTRACT(`push_notifications`.`action`, '$.class') = 'Survey' THEN (select LOWER(`surveys`.`title`) from surveys where CAST(id AS CHAR) = JSON_EXTRACT(`push_notifications`.`action`, '$.item'))
                                WHEN JSON_EXTRACT(`push_notifications`.`action`, '$.class') = 'Voucher' THEN (select LOWER(`vouchers`.`title`) from vouchers where CAST(id AS CHAR) = JSON_EXTRACT(`push_notifications`.`action`, '$.item'))
                            END) like ?", "%{$keyword}%"
                        );
                });
            });

        $rawColumns = ["notification", "created_at", "action"];
        if ($this->isImageEnabled) {
            $rawColumns[] = "image";
            $datatable->editColumn("image", function (PushNotification $model) {
                if (empty($model->image)) {
                    return __("general.message.not_provided");
                }
                return DataTableRenderHelper::renderImage($model, "image");
            });
        }

        if ($this->isLargeIconEnabled) {
            $rawColumns[] = "icon";
            $datatable->editColumn("icon", function (PushNotification $model) {
                if (empty($model->icon)) {
                    return __("general.message.not_provided");
                }
                return DataTableRenderHelper::renderImage($model, "icon");
            });
        }

        return $datatable
            ->rawColumns($rawColumns)
            ->editColumn("created_at", function (PushNotification $model) {
                return DataTableRenderHelper::renderDateTime($model, "created_at");
            })
            ->filterColumn("created_at", DataTableRenderHelper::filterDateTime("created_at"));
    }

    /**
     * Get query source of dataTable.
     *
     * @param PushNotification $model
     * @return Builder
     */
    public function query(PushNotification $model): Builder
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
            ->orders([count($this->getColumns()) - 1, "desc"])
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
                        window.location.href = '" . route("user.edit", ["user_id" => ":id"]) . "'.replace(':id', data.id);
                    })
                }",*/
                "initComplete" => "function() {
                    $('#" . $this->builder()->getTableId() . "').bind('DOMNodeInserted', function(e) {
                        KTMenu.createInstances();
                    });
                }",
                "drawCallback" => "function() { KTMenu.createInstances(); initDataTableEmptyState('". $this->builder()->getTableId() ."', this.api()); }",
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
        $columns = [
            Column::make("notification")
                ->title(__("notification.index.table_header.notification"))
                ->addClass("align-top")
                ->width("40%")
                ->responsivePriority(-1),
            Column::make("action")
                ->title(__("notification.index.table_header.action"))
                ->addClass("align-top")
                ->width("30%")
                ->responsivePriority(-1),
        ];

        if ($this->isImageEnabled) {
            $columns[] = Column::computed("image")
                ->title(__("notification.index.table_header.image"))
                ->addClass("align-top")
                ->width("10%");
        }

        if ($this->isLargeIconEnabled) {
            $columns[] = Column::computed("icon")
                ->title(__("notification.index.table_header.icon"))
                ->addClass("align-top")
                ->width("10%");
        }

        $columns[] = Column::make("created_at")
            ->title(__("notification.index.table_header.sent_at"))
            ->addClass("align-top")
            ->width("10%");

        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return "PushNotification_" . date("YmdHis");
    }
}
