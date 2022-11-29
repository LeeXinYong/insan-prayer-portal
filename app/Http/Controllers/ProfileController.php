<?php

namespace App\Http\Controllers;

use App\Http\Requests\StripTagRequest as Request;
use App\Jobs\SendEmail;
use App\Models\Country;
use App\Models\User;
use App\Services\DateTimeFormatterService;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function view(): View
    {
        /** @var User $profile */
        $profile = Auth::user();
        if($profile->last_login != __("general.message.not_applicable")) {
            $profile->last_login_duration = DateTimeFormatterService::formatIntervals($profile->last_login_epoch);
        }

        $countries = Country::query()->orderBy("name")->get();

        return view("pages.profile.view", compact("profile", "countries"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator("profile", "edit", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $user = Auth::user();
            $user->name = $request->get("name");
            $user->email = $request->get("email");
            $user->mobile = $request->get("mobile");
            $user->country_code = $request->get("country");
            $user->timezone = $request->get("timezone");

            // Before save, get change value (new) and original value (old) of user
            $changes = LoggerController::getChangedData($user);

            $user->save();

            // Log Audit
            LoggerController::log("users", $user, "audit_log.message.update_user", $user->name, $changes);

            return response()->json([
                "success" => __("profile.message.success_update"),
                "button" => __("profile.button.ok"),
                "redirect" => route("profile.view") . "#setting_card"
            ]);
        }
    }

    /**
     * Display the specified resource's password change.
     *
     * @return View
     */
    public function changePassword(): View
    {
        return view("pages.profile.change-password");
    }

    /**
     * Update the specified resource's password in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator("profile", "change-password", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $user = Auth::user();
            $user->password = bcrypt($request->get("new_password"));
            $user->force_pwd = 0;
            $user->updated_by = Auth::user()->id;
            $user->updated_ip = request()->ip();
            $user->save();

            // send email to user
            $data = array(
                "user_name" => $user->name,
                "login_url" => route("login"),
                "buttons" => [
                    ["url" => route("login"), "text" => "Sign In Now"],
                ]
            );
            SendEmail::dispatch($user, $data, "password_changed");

            Auth::logout();

            // Log Audit
            LoggerController::log("users", $user, "audit_log.message.change_password", $user->name);

            return response()->json([
                "success" => __("profile.message.success_change_password"),
                "button" => __("profile.button.re_login"),
                "redirect" => route("login")
            ]);
        }
    }

    /**
     * Display the specified resource's password change.
     *
     * @return View
     */
    public function firstTimeLogin()
    {
        if (Auth::user()->force_pwd) {
            return view('auth.update-initial-password');
        } else {
            return redirect()->route('profile.changePassword');
        }
    }

    public function updateInitialPassword(Request $request)
    {
        $validator = ValidationService::getValidator("profile", "change-initial-password", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $user = Auth::user();
            $user->password = bcrypt($request->get("password"));
            $user->force_pwd = 0;
            $user->updated_by = Auth::user()->id;
            $user->updated_ip = request()->ip();
            $user->save();

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
            LoggerController::log("users", $user, "audit_log.message.change_password", $user->name);

            return response()->json([
                "success" => __("profile.message.success_change_password"),
                "button" => __("general.button.ok"),
                "redirect" => url("/")
            ]);
        }
    }
}
