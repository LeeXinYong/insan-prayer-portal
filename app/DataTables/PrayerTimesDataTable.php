<?php

namespace App\DataTables;

use App\Models\PrayerTime;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PrayerTimesDataTable extends DataTable
{
    public const TABLE_NAME = "prayer_times";
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', 'prayertimesdatatable.action');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\PrayerTimesDataTable $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PrayerTime $model)
    {
        return $model->newQuery()->get();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId(self::TABLE_NAME)
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create'),
                        Button::make('export'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
            Column::make('prayer_id'),
            Column::make('state_id'),
            Column::make('zone_id'),
            Column::make('gregorian_date'),
            Column::make('imsak'),
            Column::make('fajr'),
            Column::make('syuruk'),
            Column::make('dhuhr'),
            Column::make('asr'),
            Column::make('maghrib'),
            Column::make('isha'),
            Column::make('updated_at'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'PrayerTimes_' . date('YmdHis');
    }
}
