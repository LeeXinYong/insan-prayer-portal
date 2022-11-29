<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class LoggerController extends Controller
{
    public static function log($log_name, $model, $activity, $log, $properties = array(), $causer = null)
    {
        activity($log_name)
            ->performedOn($model)
            ->causedBy(($causer == null) ? Auth::user() : $causer)
            ->withProperties($properties)
            ->log($activity.":".$log);
    }

    public static function getChangedData($instance): array
    {
        $changes = [];

        foreach ($instance->getDirty() as $index => $change) {
            switch (gettype($instance->getOriginal($index))) {
                case "double" : $change = (float) $change; break;
                case "integer" : $change = (int) $change; break;
                default :
            }

            $changes[$index] = [
                "new" => $change,
                "old" => $instance->getOriginal($index)
            ];
        }

        return $changes;
    }

    public static function transformLogs($logs): Collection
    {
        $logs->transform(function($log) {
            $activity = explode(":", $log->description);
            $log->activity = __($activity[0]);
            $log->description = $activity[1] ?? "";
            $log->created_at_format = Carbon::parse($log->created_at)->setTimezone(Auth::user()->timezone ?? config("app.timezone"))->format("d M Y, h:i A");
            $log->created_at_date = Carbon::parse($log->created_at)->setTimezone(Auth::user()->timezone ?? config("app.timezone"))->format("d M Y");
            $log->state_color = self::setLogStateColor($log->log_name);

            return $log;
        });

        return $logs;
    }

    public static function setLogStateColor($log)
    {
        // get all log_name
        $log_names = Activity::query()->distinct("log_name")->pluck("log_name")->toArray();
        // state colors
        $states = array("success", "warning", "danger", "info", "primary", "secondary", "dark");

        // extend the array by duplicating
        $new_states = extend_array(count($states), count($log_names), $states);

        foreach ($log_names as $index => $log_name) {
            if ($log_name == $log) {
                return $new_states[$index];
            }
        }

        return "dark";
    }
}
