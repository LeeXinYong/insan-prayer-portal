<?php

namespace App\Http\Controllers\Logs;

use App\DataTables\Logs\DownloadLogsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\Video;

class DownloadLogsController extends Controller
{

    public function __construct()
    {
        $this->authorizeMethod("index", "viewAny", Download::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @param DownloadLogsDataTable $dataTable
     * @return mixed
     */
    public function index(DownloadLogsDataTable $dataTable): mixed
    {
        $modules = Download::query()
            ->distinct("downloadable_type")
            ->pluck("downloadable_type")
            ->transform(function ($module) {
                return [$module, __("download_log.module." . str_replace("App\\Models\\", "", $module))];
            });
        return $dataTable->render("pages.log.download.index", compact("modules"));
    }
}
