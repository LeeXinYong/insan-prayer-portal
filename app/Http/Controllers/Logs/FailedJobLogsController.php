<?php

namespace App\Http\Controllers\Logs;

use App\DataTables\Logs\FailedJobLogsDataTable;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class FailedJobLogsController extends Controller
{
    public function __construct()
    {
        $this->authorizeMethod("index", "viewAny", "FailedJobLog");
    }

    /**
     * Display a listing of the resource.
     *
     * @param FailedJobLogsDataTable $dataTable
     * @return mixed
     */
    public function index(FailedJobLogsDataTable $dataTable): mixed
    {
        return $dataTable->render("pages.log.failed_job.index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     *
     * @return ResponseFactory|Application|Response
     */
    public function destroy(string $id): Response|Application|ResponseFactory
    {
        try {
            if ($id !== "all") {
                // delete single
                DB::table("failed_jobs")->where("id", $id)->delete();
            } else if ($id === "all") {
                // truncate
                DB::table("failed_jobs")->truncate();
            }

            return response([
                "message" => __("failed_job_log.message.all_jobs_successfully_deleted")
            ], 200);
        } catch (Exception $e) {
            return response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Retry specific job
     *
     * @param  string  $id
     *
     * @return ResponseFactory|Application|Response
     */
    public function retry(string $id): ResponseFactory|Application|Response
    {
        $uuid = $id;
        if($id !== "all") {
            $job = DB::table("failed_jobs")->find($id);
            $uuid = $job->uuid;
        }

        try {
            Artisan::call("queue:retry", ["id" => $uuid]);

            return response([
                "message" => __("failed_job_log.message.job_pushed_back_onto_queue", ["id" => $uuid])
            ], 200);
        } catch (Exception $e) {
            return response($e->getMessage(), $e->getCode());
        }
    }
}
