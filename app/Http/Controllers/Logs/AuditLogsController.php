<?php

namespace App\Http\Controllers\Logs;

use App\DataTables\Logs\AuditLogsDataTable;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class AuditLogsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Activity::class, 'audit');
    }

    /**
     * Display a listing of the resource.
     *
     * @param AuditLogsDataTable $dataTable
     * @return mixed
     */
    public function index(AuditLogsDataTable $dataTable): mixed
    {
        $modules = Activity::query()
            ->distinct("subject_type")
            ->pluck("subject_type")
            ->transform(function ($module) {
                $subject_type = explode("\\", $module);
                return [$module, __("audit_log.module." . end($subject_type))];
            })
            ->sortBy(function($item) {
                return $item[1];
            });

        return $dataTable->render("pages.log.audit.index", compact("modules"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy(Activity $audit)
    {
        // Delete from db
        $audit->delete();
    }
}
