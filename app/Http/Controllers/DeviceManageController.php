<?php

namespace App\Http\Controllers;

use App\Http\Requests\StripTagRequest as Request;
use App\Jobs\SendEmail;
use App\Models\DeviceLogin;
use App\Models\DeviceManage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;

class DeviceManageController extends Controller
{
    public function trustDevice(Request $request): Factory|View|Application
    {
        $token = $request->route("token");

        // Get device manage and device login
        $device_manage = DeviceManage::query()->where("trust_token", "=", $token)->firstOrFail();
        $device_login = DeviceLogin::query()->findOrFail($device_manage->device_login ?? "");

        // Set device status to trusted
        $device_login->update(["status" => DeviceLogin::DEVICE_TRUSTED]);

        // Delete token
        DeviceManage::query()->where("trust_token", "=", $token)->delete();

        $device_name = $device_login->user_agent;

        return view("pages.device_login.trust", compact("device_name"));
    }

    public function blockDevice(Request $request): Factory|View|Application
    {
        $token = $request->route("token");

        // Get device manage and device login
        $device_manage = DeviceManage::query()->where("block_token", "=", $token)->firstOrFail();
        $device_login = DeviceLogin::query()->findOrFail($device_manage->device_login ?? "");

        // Set device status to blocked
        $device_login->update(["status" => DeviceLogin::DEVICE_BLOCKED]);

        // Set current user remaining session to expired
        DeviceLogin::query()->where("user_id", "=", $device_login->user_id)->update(["session_expired_at" => Carbon::now()]);

        $device_name = $device_login->user_agent;

        return view("pages.device_login.block", compact("token", "device_name"));
    }

    public function updateNewPassword(Request $request): JsonResponse
    {
        $request->validate([
            "token"    => "required|exists:device_manages,block_token",
            "password" => ["required", "confirmed", Password::defaults()],
        ]);

        $device_manage = DeviceManage::query()->where("block_token", "=", $request->get("token"))->firstOrFail();
        $device_login = DeviceLogin::query()->findOrFail($device_manage->device_login ?? "");

        $user = User::query()->find($device_login->user_id);
        $user->password = bcrypt($request->get("password"));
        $user->force_pwd = 0;
        $user->updated_by = $user->id;
        $user->updated_ip = request()->ip();
        $user->save();

        // Delete token
        DeviceManage::query()->where("block_token", "=", $request->get("token"))->delete();

        // send email to user
        $data = array(
            "user_name" => $user->name,
            "login_url" => route("login"),
            "buttons" => [
                ["url" => route("login"), "text" => "Sign In Now"],
            ]
        );
        SendEmail::dispatch($user, $data, "password_changed");

        // Log Audit
        LoggerController::log("users", $user, "audit_log.message.change_password", $user->name, causer: $user);

        return response()->json([
            "message" => __("auth.reset_password.success_reset"),
            "button" => __("auth.reset_password.login"),
            "redirect" => route("login")
        ]);
    }
}
