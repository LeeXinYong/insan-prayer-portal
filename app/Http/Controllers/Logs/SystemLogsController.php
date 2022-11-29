<?php

namespace App\Http\Controllers\Logs;

use App\DataTables\Logs\SystemLogsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StripTagRequest as Request;
use Illuminate\Support\Facades\Config;
use Jackiedo\LogReader\LogReader;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SystemLogsController extends Controller
{
    public function __construct()
    {
        $this->authorizeMethod("index", "viewAny", "SystemLog");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SystemLogsDataTable $dataTable): mixed
    {
        $logChannels = $dataTable->getLogChannels();
        $logFiles = [];

        foreach($logChannels as $channel => $logChannel) {
            $logFiles[str($channel)->replace(".", "-")->value()] = $this->getLogFilenameList($channel);
        }

        return $dataTable->render('pages.log.system.index', compact('logChannels', 'logFiles'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, LogReader $logReader)
    {
        return $logReader->find($id)->delete();
    }

    /**
     * Returns an array of log file paths.
     *
     * @param $logChannel
     * @return bool|array
     */
    public function getLogFilenameList($logChannel): bool|array
    {
        $data = [];

        if (is_null($logChannel) || !Config::has('logging.channels.'.$logChannel.'.path')) {
            $logChannel = config('logging.default');
        }

        $path = str_replace("/laravel.log", "", config('logging.channels.'.$logChannel.'.path'));

        if (is_dir($path)) {

            /*
             * Matches files in the log directory with the special name'
             */
            $logPath = sprintf('%s%s%s', $path, DIRECTORY_SEPARATOR, '*.*');

            $files = glob($logPath, GLOB_BRACE);
        } else {
            $files = false;
        }

        if (is_array($files)) {
            foreach ($files as $file) {
                $basename = pathinfo($file, PATHINFO_BASENAME);
                $data[$basename] = str($basename)->replace("laravel-", "")->replace(".log", "");
            }

            if(isset($data["laravel.log"])) {
                unset($data["laravel.log"]);
            }
        }

        return array_reverse($data);
    }

    public function download(Request $request): BinaryFileResponse
    {
        $channel = $request->channel;
        $file = $request->file;
        $full_path = "logs/" . $file;

        if($channel != "default" && $channel != "") {
            $path = str($channel)->replace("-", "/");
            $full_path = "logs/" . $path . "/" . $file;
        }

        // check if file exist
        if (!file_exists(storage_path($full_path))) {
            abort(404);
        }

        return response()->download(storage_path($full_path));
    }
}
