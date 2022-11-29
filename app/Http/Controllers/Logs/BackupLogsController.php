<?php

namespace App\Http\Controllers\Logs;

use App\DataTables\Logs\BackupLogsDataTable;
use App\Http\Requests\StripTagRequest as Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\BackupLog;
use Artisan;

class BackupLogsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(BackupLog::class, 'backup');
    }

    /**
     * Display a listing of the resource.
     *
     * @param BackupLogsDataTable $dataTable
     * @return mixed
     */
    public function index(BackupLogsDataTable $dataTable): mixed
    {
        return $dataTable->render("pages.log.backup.index");
    }

    public function getBackupDestinationStatus(Request $request): JsonResponse
    {
        Artisan::call('backup:list');

        $output =  Artisan::output();
        $destinationListTable = [];
        $unhealthyDestinationListTable = [];

        if($output != null || $output != "") {
            $cleanOutput = str_replace("-", "", $output);
            $cleanOutput = str_replace("+", "", $cleanOutput);

            $outputArr = explode(PHP_EOL, $cleanOutput);
            $outputArr = array_filter($outputArr);
            $outputArr = array_values($outputArr);

            $separatorIndex = array_search('Unhealthy backup destinations', $outputArr);

            if($separatorIndex != false) {
                unset($outputArr[$separatorIndex]);
                $outputArr = array_values($outputArr);

                $outputChunks = array_chunk($outputArr, $separatorIndex);

                $destinationList = isset($outputChunks[0]) ? $outputChunks[0] : [];
                $unhealthyDestinationList = isset($outputChunks[1]) ? $outputChunks[1] : [];

                $destinationListTable = self::getTableArray($destinationList);
                $unhealthyDestinationListTable = self::getTableArray($unhealthyDestinationList);
            } else {
                $destinationListTable = self::getTableArray($outputArr);
            }
        }

        return response()->json([
            'destinationList' => $destinationListTable,
            'unhealthyDestinationList' => $unhealthyDestinationListTable,
        ]);
    }

    public static function getTableArray($arr)
    {
        $tableArray = [];
        $i = 0;
        foreach ($arr as $row) {
            $row = trim($row, "|");

            if ($i == 0) {
                $thead = preg_split('/\s*\|\s*/', trim($row), -1, PREG_SPLIT_NO_EMPTY);
                $tableArray["thead"] = self::translate($thead);
            } else {
                $tableArray["tbody"][] = preg_split('/\s*\|\s*/', trim($row), -1, PREG_SPLIT_NO_EMPTY);
            }
            $i++;
        }

        return $tableArray;
    }

    public static function translate($arr)
    {
        $translatedArr = [];
        foreach ($arr as $row) {
            $translatedArr[] = __('backup_log.destination_status.table_header.'.$row);
        }

        return $translatedArr;
    }

    public static function log($disk_name, $backup_name, $event, $message,  $status = 1)
    {
        $cleanedMessage = $message;
        $stackTrace = "";

        if(!$status) {
            // Message
            $exc = explode("\n", $message, 2);
            $cleanedMessage = (isset($exc[0])) ? trim($exc[0]) : trim($message);

            // Stack Trace
            $exc = explode("\n", $message, 2);
            $stackTrace = (isset($exc[1])) ? trim($exc[1]) : trim($message);
        }

        /// get env APP_NAME

        $backupLog = new BackupLog();
        $backupLog->disk_name = $disk_name;
        $backupLog->backup_name = $backup_name;
        $backupLog->event = $event;
        $backupLog->message = $cleanedMessage;
        $backupLog->stack_trace = $stackTrace;
        $backupLog->status = $status;
        $backupLog->save();
    }

    public static function guessDiskName($message)
    {
        $disks = \Config('backup.backup.destination.disks');
        $disks = (isset($disks)) ? $disks : [];

        foreach ($disks as $disk) {
            if(strpos($message, $disk) !== false) {
                return $disk;
            }
        }

        return "";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy(BackupLog $backup)
    {
        // Delete from db
        $backup->delete();
    }
}
