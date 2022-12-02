<?php

namespace App\DataTables;

use App\Models\PrayerTime;
use App\Models\Zone;
use App\Services\DataTableRenderHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PrayerTimesDataTable extends DataTable
{
    public const TABLE_NAME = "prayer_time_table";
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query): DataTableAbstract
    {
        return datatables()
            ->eloquent($query)            
            ->rawColumns(["zone_id", "name", "gregorian_date", "imsak", "fajr", "syuruk", "dhuhr", "asr", "maghrib", "isha"])
            ->addColumn("action", function (PrayerTime $model) {
                $actions = [
                    "edit" => [
                        "url" => route("prayer_time.edit", ["prayer_time" => $model->prayer_id]),
                        "label" => __("general.button.edit"),
                        "disabled" => Auth::user()->cannotUpdate(PrayerTime::class)
                    ]
                ];

                $forceDropdown = true;

                return view("pages.common-components.buttons.action-menu-button", compact("actions", "forceDropdown"));
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\PrayerTimesDataTable $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PrayerTime $model): Builder
    {
        return $model->newQuery()->join('zones', 'prayer_times.zone_id', '=', 'zones.zone_id')->select('prayer_times.*', 'zones.name')->orderBy('gregorian_date');
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
                            $(row).attr('data-id', data.prayer_id);
                        }",
                        // "initComplete" => "function() {
                        //     initDataTableRowRearrange('" . $this->builder()->getTableId() . "', this.api(), '" . route("banner.rearrange") . "');
                        //     $('#" . $this->builder()->getTableId() . "').bind('DOMNodeInserted', function(e) {
                        //         KTMenu.createInstances();
                        //     });
                        // }",
                        "drawCallback" => "function() {
                            KTMenu.createInstances();
                            refreshFsLightbox();

        
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
            Column::make('zone_id')
                ->title( __("prayer_time.form_label.zone_id") )
                ->name('prayer_times.zone_id')
                ->width("5%"),
            Column::make('name')
                ->title( __("prayer_time.form_label.name") )
                ->name('zones.name'),
            Column::make('gregorian_date')
                ->title( __("prayer_time.form_label.gregorian_date") )
                ->name('prayer_times.gregorian_date')
                ->width("8%")
                ->orderable(false),
            Column::make('imsak')
                ->title( __("prayer_time.form_label.imsak") )
                ->name('prayer_times.imsak')
                ->width("8%")
                ->searchable(false)
                ->orderable(false),
            Column::make('fajr')
                ->title( __("prayer_time.form_label.fajr") )
                ->name('prayer_times.fajr')
                ->width("8%")
                ->searchable(false)
                ->orderable(false),
            Column::make('syuruk')
                ->title( __("prayer_time.form_label.syuruk") )
                ->name('prayer_times.syuruk')
                ->width("8%")
                ->searchable(false)
                ->orderable(false),
            Column::make('dhuhr')
                ->title( __("prayer_time.form_label.dhuhr") )
                ->name('prayer_times.dhuhr')
                ->width("8%")
                ->searchable(false)
                ->orderable(false),
            Column::make('asr')
                ->title( __("prayer_time.form_label.asr") )
                ->name('prayer_times.asr')
                ->width("8%")
                ->searchable(false)
                ->orderable(false),
            Column::make('maghrib')
                ->title( __("prayer_time.form_label.maghrib") )
                ->name('prayer_times.maghrib')
                ->width("8%")
                ->searchable(false)
                ->orderable(false),
            Column::make('isha')
                ->title( __("prayer_time.form_label.isha") )
                ->name('prayer_times.isha')
                ->width("8%")
                ->searchable(false)
                ->orderable(false),
            Column::computed("action")
                ->title("Action")
                ->searchable(false)
                ->orderable(false)
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
        return 'PrayerTimes_' . date('YmdHis');
    }
}
