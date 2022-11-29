<?php

namespace App\DataTables\Logs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Jackiedo\LogReader\Exceptions\UnableToRetrieveLogFilesException;
use Jackiedo\LogReader\LogReader;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Services\DataTable;

class SystemLogsDataTable extends DataTable
{
    protected ?string $logChannel = null;

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
            ->collection($query)
            ->rawColumns(['action', 'level'])
            ->editColumn('id', function (Collection $model) {
                return Str::limit($model->get('id'), 5, '');
            })
            ->editColumn('file_path', function (Collection $model) {
                return Str::limit($model->get('file_path'));
            })
            ->editColumn('message', function (Collection $model) {
                return Str::limit($model->get('context')->message, 95);
            })
            ->editColumn('date', function (Collection $model) {
                return $model->get('date')->format('d M, Y H:i:s');
            })
            ->editColumn('level', function (Collection $model) {
                $styles = [
                    'emergency' => 'danger',
                    'alert'     => 'warning',
                    'critical'  => 'danger',
                    'error'     => 'danger',
                    'warning'   => 'warning',
                    'notice'    => 'success',
                    'info'      => 'info',
                    'debug'     => 'primary',
                ];
                $style  = 'info';
                if (isset($styles[$model->get('level')])) {
                    $style = $styles[$model->get('level')];
                }
                $value = $model->get('level');

                return '<div class="badge badge-light-'.$style.' fw-bolder">'.$value.'</div>';
            })
            ->editColumn('context', function (Collection $model) {
                $content = $model->get('context');

                return view('pages.log.system._details', compact('content'));
            })
            ->addColumn("action", function (Collection $model) {
                $actions = array(
                    "delete" => [
                        "url" => route("system.log.system.destroy", $model->get('id')),
                        "label" => __("general.button.delete"),
                        "disabled" => Auth::user()->cannotDelete("SystemLog")
                    ]
                );

                $forceDropdown = true;

                return view("pages.common-components.buttons.action-menu-button", compact("actions", "forceDropdown"));
            })
            ->addColumn('date_raw', function (Collection $model) {
                return $model->get('date');
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param  LogReader  $model
     *
     * @return Collection
     */
    public function query(LogReader $model): Collection
    {
        $data = collect();

        $logPath = $this->getLogPath();

        $model->setLogPath($logPath);

        $files = $model->getLogFilenameList();

        if(isset($files["laravel.log"])) {
            unset($files["laravel.log"]);
        }

        if(($this->request()->get("filter_file") ?? "") != "") {
            $model->filename($this->request()->get("filter_file"));
        } else {
            $model->filename(array_key_last($files));
        }

        try {
            $data = $model->get()->merge($data);
        } catch (UnableToRetrieveLogFilesException $exception) {
        }

        return $data->map(function ($a) {
            return (collect($a))->only(['id', 'date', 'environment', 'level', 'file_path', 'context']);
        });
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return Builder
     */
    public function html(): Builder
    {
        $parameters = [
            "log_channel" => $this->logChannel,
            "filter_file" => "$('#filterFile').val()",
        ];

        return $this->builder()
            ->setTableId('system-log-'.str($this->logChannel)->replace(".", "-").'-table')
            ->columns($this->getColumns())
            ->pageLength(50)
            ->minifiedAjax('', null, $parameters)
            ->stateSave(false)
            ->orderBy(3)
            ->responsive()
            ->dom("<'table-responsive'tr><'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>")
            ->autoWidth(false)
            ->parameters([
                "scrollX" => true,
                "columnDefs" => [
                    [
                        'targets' => [3], // order table by raw date which is hidden in column 8
                        'orderData' => [8],
                    ],
                ],
                "initComplete" => "function() {
                    $('#" . $this->builder()->getTableId() . "').bind('DOMNodeInserted', function(e) {
                        KTMenu.createInstances();
                    });
                }",
                "drawCallback" => "function() { KTMenu.createInstances(); }",
            ])
            ->addTableClass('align-middle table-row-dashed fs-6 gy-5');
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return [
            Column::make('id')
                ->title(__('system_log.table_header.log_id'))
                ->width(100)
                ->addClass('ps-0')
                ->responsivePriority(-1),
            Column::make('message')
                ->title(__('system_log.table_header.message')),
            Column::make('level')
                ->title(__('system_log.table_header.level')),
            Column::make('date')
                ->title(__('system_log.table_header.date'))
                ->width(200),
            Column::computed('action')
                ->title(__('system_log.table_header.action'))
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center')
                ->responsivePriority(-1),
            Column::make('environment')
                ->title(__('system_log.table_header.environment'))
                ->addClass('none'),
            Column::make('file_path')
                ->title(__('system_log.table_header.file_path'))
                ->addClass('none'),
            Column::make('context')
                ->title(__('system_log.table_header.context'))
                ->addClass('none'),
            Column::make('date_raw')
                ->visible(false)
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
        return 'SystemLogs_'.$this->logChannel.date('YmdHis');
    }

    protected function getLogPath() : string
    {
        $logChannel = $this->logChannel;

        if (is_null($logChannel) || !Config::has('logging.channels.'.$logChannel.'.path')) {
            $logChannel = config('logging.default');
        }

        return str_replace("/laravel.log", "", config('logging.channels.'.$logChannel.'.path'));
    }

    public function setLogChannel($logChannel): static
    {
        $this->logChannel = $logChannel;
        return $this;
    }

    /**
     * Process dataTables needed render output.
     *
     * @param  string  $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return mixed
     */
    public function render($view, $data = [], $mergeData = []): mixed
    {
        $logChannels = array_keys($this->getLogChannels());

        if ($this->request()->has("log_channel") && in_array($this->request()->get("log_channel"), $logChannels)) {
            $this->setLogChannel($this->request()->get("log_channel"));
        }

        if ($this->request()->ajax() && $this->request()->wantsJson()) {
            return app()->call([$this, 'ajax']);
        }

        if ($action = $this->request()->get('action') and in_array($action, $this->actions)) {
            if ($action == 'print') {
                return app()->call([$this, 'printPreview']);
            }

            return app()->call([$this, $action]);
        }

        $htmlBuilders = collect($logChannels)
            ->mapWithKeys(function ($logChannel) {
                return [$logChannel => (clone $this)->setLogChannel($logChannel)->getHtmlBuilder()];
            })
            ->toArray();

        return view($view, $data, $mergeData)->with($this->dataTableVariable, $htmlBuilders);
    }

    public function getLogChannels() : array
    {
        return config("logging.viewable");
    }
}
