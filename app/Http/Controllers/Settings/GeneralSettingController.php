<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StripTagRequest as Request;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\SysParam;

class GeneralSettingController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SysParam::class, "general");
    }

    protected function resourceMethodsWithoutModels()
    {
        return [
            ...parent::resourceMethodsWithoutModels(),
            'update',
        ];
    }

    public function index(Request $request)
    {
        $timeout = SysParam::get('timeout');
        $timeout_duration = SysParam::get('timeout_duration');
        $timeout_countdown = SysParam::get('timeout_countdown');
        $recaptcha = SysParam::get('recaptcha');
        $recaptcha_max_attempt = SysParam::get('recaptcha_max_attempt');
        $failed_job_email_alert = SysParam::get('failed_job_email_alert');
        $failed_job_webhook_alert = SysParam::get('failed_job_webhook_alert');
        $web_portal_concurrent_login = SysParam::get('web_portal_concurrent_login');
        $mobile_app_concurrent_login = SysParam::get('mobile_app_concurrent_login');

        return view('pages.settings.general.edit', compact('timeout_countdown', 'timeout_duration', 'timeout', 'recaptcha', 'recaptcha_max_attempt', 'failed_job_email_alert', 'failed_job_webhook_alert', 'web_portal_concurrent_login', 'mobile_app_concurrent_login'));
    }

    public function update(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator("settings.general", "edit", request: $request);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            //update or insert if has timeout
            $timeout_bool = ($request->has('timeout') && $request->get('timeout') === "on") ?
                SysParam::updateOrCreate('timeout', true) &&
                SysParam::updateOrCreate('timeout_duration', $request->get('timeout_duration')) &&
                SysParam::updateOrCreate('timeout_countdown', $request->get('timeout_countdown'))
                : SysParam::updateOrCreate('timeout', false);

            //update or insert if has recaptcha
            $recaptcha_bool = ($request->has('recaptcha') && $request->get('recaptcha') === "on") ?
                SysParam::updateOrCreate('recaptcha', true) &&
                SysParam::updateOrCreate('recaptcha_max_attempt', $request->get('recaptcha_max_attempt'))
                : SysParam::updateOrCreate('recaptcha', false);

            //update or insert if has failed job email alert
            $failed_job_monitor_email_bool = ($request->has('failed_job_email_alert') && $request->get('failed_job_email_alert') === "on") ?
            SysParam::updateOrCreate('failed_job_email_alert', true) : SysParam::updateOrCreate('failed_job_email_alert', false);

            //update or insert if has failed job webhook alert
            $failed_job_monitor_webhook_bool = ($request->has('failed_job_webhook_alert') && $request->get('failed_job_webhook_alert') === "on") ?
            SysParam::updateOrCreate('failed_job_webhook_alert', true) : SysParam::updateOrCreate('failed_job_webhook_alert', false);

            //update or insert if has failed job webhook alert
            $failed_job_monitor_webhook_bool = ($request->has('web_portal_concurrent_login') && $request->get('web_portal_concurrent_login') === "on") ?
            SysParam::updateOrCreate('web_portal_concurrent_login', true) : SysParam::updateOrCreate('web_portal_concurrent_login', false);

            //update or insert if has failed job webhook alert
            $failed_job_monitor_webhook_bool = ($request->has('mobile_app_concurrent_login') && $request->get('mobile_app_concurrent_login') === "on") ?
            SysParam::updateOrCreate('mobile_app_concurrent_login', true) : SysParam::updateOrCreate('mobile_app_concurrent_login', false);

            if ($timeout_bool && $recaptcha_bool && $failed_job_monitor_email_bool && $failed_job_monitor_webhook_bool) {
                return response()->json([
                    "success" => __("settings.general.message.success_update"),
                    "button" => __("general.button.ok"),
                    "redirect" => route('system.settings.general.index')
                ]);
            } else {
                return response()->json([
                    "error" => __("settings.general.message.fail_update"),
                    "button" => __("general.button.ok"),
                    "redirect" => null
                ]);
            }
        }
    }
}
