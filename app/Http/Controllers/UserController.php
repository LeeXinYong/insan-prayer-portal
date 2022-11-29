<?php

namespace App\Http\Controllers;

use App\DataTables\Permissions\UserPermissionsDataTable;
use App\DataTables\UsersDataTable;
use App\Http\Requests\StripTagRequest as Request;
use App\Jobs\SendEmail;
use App\Models\Country;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use App\Notifications\TestNotification;
use App\Services\DateTimeFormatterService;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Carbon\Carbon;

class UserController extends Controller
{
    protected function resourceAbilityMap(): array
    {
        return [
            ...parent::resourceAbilityMap(),
            'updateStatus' => 'updateStatus',
            'updatePassword' => 'updatePassword',
            'sendTestNotification' => 'sendTestNotification',
        ];
    }

    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     *
     * @param UsersDataTable $dataTable
     * @return mixed
     */
    public function index(UsersDataTable $dataTable): mixed
    {
        $roles = RoleController::getRoles();

        return $dataTable->render("pages.user.index", compact("roles"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $countries = Country::query()->orderBy("name")->get();
        return view("pages.user.create", compact("countries"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator( "user", "create", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $user = new User;
            $user->name = $request->get("name");
            $user->email = $request->get("email");
            $password = Str::random(8);
            $user->password = bcrypt($password);
            $user->mobile = $request->get("mobile");
            $user->country_code = $request->get("country");
            $user->timezone = $request->get("timezone");
            $user->status = $request->status ?? 0;

            // Before save, get change value (new) and original value (old) of user
            $changes = LoggerController::getChangedData($user);

            $user->save();

            // send email to user
            $data = array(
                "user_name" => $user->name,
                "login_id" => $user->email,
                "login_password" => $password,
                "login_url" => config('app.url').'/login',
                "buttons" => [
                    ["url" => config('app.url').'/login', "text" => "Sign In Now"]
                ]
            );
            SendEmail::dispatch($user, $data, "new_user");

            // Log Audit
            LoggerController::log("users", $user, "audit_log.message.create_user", $user->name, $changes);

            return response()->json([
                "success" => __("user.message.success_create"),
                "button" => __("user.button.view_listing"),
                "redirect" => route("user.index")
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param UserPermissionsDataTable $datatable
     * @param User $user
     * @return View|JsonResponse
     */
    public function show(Request $request, UserPermissionsDataTable $datatable, User $user): View|JsonResponse
    {
        if ($user->last_login != __("general.message.not_applicable")) {
            $user->last_login_duration = DateTimeFormatterService::formatIntervals($user->last_login_epoch);
        }

        if ($request->ajax() && $request->has('profile_only')) {
            $user->roles = $user->getRoleNames();

            $country_timezone = __("general.message.not_applicable");
            if($user->country) {
                $country_timezone = $user->country->name . ", " . $user->timezoneInfo->timezone_name . " " . $user->timezoneInfo->offset;

                if (isset($user->country->flag_icon_svg)) {
                    $country_timezone = theme()->getSvgIcon($user->country->flag_icon_svg, "svg-icon-3 svg-icon-success me-2") . $country_timezone;
                }
            }

            $user->country_timezone = $country_timezone;

            return response()->json($user);
        }

        $countries = Country::query()->orderBy("name")->get();

        return $datatable->render("pages.user.show", compact( "user", "countries"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validator = ValidationService::getValidator("user", "edit", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
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
                "success" => __("user.message.success_update"),
                "button" => __("user.button.ok"),
                "redirect" => route("user.show", ["user" => $user]) . "#setting_card"
            ]);
        }
    }

    /**
     * Update the specified resource's status in storage.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function updateStatus(Request $request, User $user): JsonResponse
    {
        if ($request->action == "reactivate") {
            $user->status = 1;
            $user->save();
        } else if ($request->action == "suspend") {
            $user->status = 0;
        } else {
            return response()->json(["errors" => [__("user.message.fail_status")]], 422);
        }

        // Before save, get change value (new) and original value (old) of user
        $changes = LoggerController::getChangedData($user);

        $user->save();

        // send email to user
        $data = array(
            "user_name" => $user->name,
        );
        SendEmail::dispatch($user, $data, $request->action . "_account");

        // Log Audit
        LoggerController::log("users", $user, "audit_log.message." . $request->action . "_user", $user->name, $changes);

        return response()->json(["success" => __("user.message.success_" . $request->action), "user_status" => $user->status]);
    }

    /**
     * Update the specified resource's password in storage.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function updatePassword(Request $request, User $user): JsonResponse
    {
        $password = Str::random(8);
        $user->password = bcrypt($password);
        $user->force_pwd = 1;
        $user->updated_by = Auth::user()->id;
        $user->updated_ip = request()->ip();

        $user->save();

        // send email to user
        $data = array(
            "user_name" => $user->name,
            "login_password" => $password,
        );
        SendEmail::dispatch($user, $data, "reset_password");

        // Log Audit
        LoggerController::log("users", $user, "audit_log.message.reset_password", $user->name);

        return response()->json(["success" => __("user.message.success_reset_password")]);
    }

    public static function canUpdateStatusAndUpdatePassword($user, $model): bool
    {
        return $user->id !== $model->id;
    }

    public function sendTestNotification(Request $request, User $user): JsonResponse
    {
        $user->notifyNow(new TestNotification());

        // Log Audit
        LoggerController::log("users", $user, "audit_log.message.send_test_notification", $user->name);

        return response()->json(["success" => __("user.message.success_send_test_notification")]);
    }

    public function getUserActivities(Request $request, User $user)
    {
        if ($request->ajax()) {
            $activities = Activity::query()
                ->where("causer_id", $user->id)
                ->orderBy("created_at", "desc")
                ->paginate(5);

            $activities->getCollection()->transform(function ($log) {
                $activity = explode(":", $log->description);
                $log->activity = __($activity[0]);
                $log->description = $activity[1] ?? "";
                $log->created_at_date = Carbon::parse($log->created_at)->setTimezone(Auth::user()->timezone ?? config("app.timezone"))->format("d M Y");
                $log->created_at_time = Carbon::parse($log->created_at)->setTimezone(Auth::user()->timezone ?? config("app.timezone"))->format("h:i A");

                return [
                    "activity" => $log->activity,
                    "description" => ($log->description != "") ? $log->description : __('general.message.not_applicable'),
                    "created_at_date" => $log->created_at_date,
                    "created_at_time" => $log->created_at_time,
                ];
            });

            return $activities;
        }

        abort(404);
    }
}
