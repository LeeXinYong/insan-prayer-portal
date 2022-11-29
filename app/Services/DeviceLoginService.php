<?php

namespace App\Services;

use App\Jobs\SendEmail;
use App\Models\DeviceLogin;
use App\Models\DeviceManage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Stevebauman\Location\Facades\Location;
use WhichBrowser\Parser;

class DeviceLoginService
{
    private ?array $user_agent_data;
    private ?string $user_agent;
    private ?User $user;

    public function __construct()
    {
        $this->user = Auth::user();
        $user_agent = new Parser(request()->header("User-Agent"));
        $this->user_agent_data = [
            "browser_name" => $user_agent->browser->name,
            "browser_version" => $user_agent->browser->version->value,
            "os_name" => ($user_agent->os->alias) ?: $user_agent->os->name,
            "os_version" => $user_agent->os->version->value,
            "device_type" => $user_agent->device->type,
            "device_model" => $user_agent->device->model
        ];
        $this->user_agent = "(". $user_agent->browser->name . " " . $user_agent->browser->version->value . ") " . (($user_agent->os->alias) ?: $user_agent->os->name) . " " . $user_agent->os->version->value . " ". $user_agent->device->type . (isset($user_agent->device->model) ? (" " . $user_agent->device->model) : "");

    }

    public function checkDevice(): DeviceLogin
    {
        // Check if new device, create the new device
        /** @var DeviceLogin $device */
        $device = DeviceLogin::query()->where([
            ["id", Cookie::get($this->user->id . "_device")],
            ["user_id", $this->user->id],
            ["user_agent", $this->user_agent]
        ])->first();
        if(!$device) {
            $device = new DeviceLogin;
            $device->user_id = $this->user->id;
            $device->user_ip = request()->ip();
            $device->user_agent = $this->user_agent;
            $device->browser_name = $this->user_agent_data["browser_name"];
            $device->browser_version = $this->user_agent_data["browser_version"];
            $device->os_name = $this->user_agent_data["os_name"];
            $device->os_version = $this->user_agent_data["os_version"];
            $device->device_type = $this->user_agent_data["device_type"];
            $device->device_model = $this->user_agent_data["device_model"];
            $position = Location::get();
            $device->location = $position->cityName . ", " . $position->regionName . ", " . $position->countryName;
        }

        // Save device login new session
        $device->session_id = Session::getId();
        $device->session_expired_at = null;
        $device->save();

        // If device status is untrusted, send email alert
        if($device->status == DeviceLogin::DEVICE_UNTRUSTED) {
            $device_manage_url = $this->generateDeviceManageUrl($device);
            $data = array(
                "user_name" => $this->user->name,
                "domain_name" => config("app.name"),
                "device" => $device->user_agent,
                "ip_address" => $device->user_ip,
                "location" => $device->location,
                "date" => $device->created_at,
                "buttons" => [
                    ["url" => $device_manage_url["trust_url"], "text" => __("general.message.yes")],
                    ["url" => $device_manage_url["block_url"], "text" => __("general.message.no")],
                ]
            );
            SendEmail::dispatch($this->user, $data, "new_device_login");
        }

        // If device status is untrusted, set current session expired
        if($device->status == DeviceLogin::DEVICE_BLOCKED) {
            $device->session_expired_at = Carbon::now();
            $device->save();
        }

        return $device;
    }

    public function generateDeviceManageUrl($device): array
    {
        $trust_token = $this->createNewToken("DEVICE=" . $device->id . "&TIME=" . Carbon::now()->timestamp);
        $block_token = $this->createNewToken("DEVICE=" . $device->id . "&TIME=" . Carbon::now()->timestamp);
        DeviceManage::query()->updateOrInsert(
            [
                "device_login" => $device->id
            ],
            [
                "trust_token" => $trust_token,
                "block_token" => $block_token,
                "created_at" => Carbon::now()
            ]
        );

        return [
            "trust_url" => route("device.trust", ["token" => $trust_token]),
            "block_url" => route("device.block", ["token" => $block_token]),
        ];
    }

    public function createNewToken($extra_data = ""): string
    {
        return hash_hmac("sha256", Str::random(40) . "$extra_data", config("app.api_key"));
    }
}
