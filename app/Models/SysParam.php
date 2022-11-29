<?php

namespace App\Models;

use App\Http\Controllers\LoggerController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SysParam extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value'
    ];

    public static function get(string $string)
    {
        return SysParam::getObject($string)->value ?? null;
    }

    private static function getObject(string $string)
    {
        return SysParam::where('key', '=', $string)->first();
    }

    public static function updateOrCreate(string $key, $value)
    {
        $sys_param = SysParam::getObject($key);
        if ($sys_param === null) {
            $sys_param = new SysParam();
            $sys_param->key = $key;
            $sys_param->added_by = Auth::user()->id;
            $sys_param->added_ip = request()->ip();
        }
        $sys_param->value =  $value;
        $sys_param->updated_by = Auth::user()->id;
        $sys_param->updated_ip = request()->ip();

        // Before save, get change value (new) and original value (old) of banner
        $changes = LoggerController::getChangedData($sys_param);
        $data_changed = (isset($changes['value']) && ($changes["value"]["new"] != $changes["value"]["old"])) ? true : false;

        if ($sys_param->save()) {
            // only log audit if there is a change
            if ($data_changed) {
                // Log Audit
                LoggerController::log($key . '_setting', $sys_param, "audit_log.message.update_general_setting_" . $key, "SysParam key - " . $sys_param->key, $changes);
            }

            return true;
        } else {
            return false;
        }
    }
}
