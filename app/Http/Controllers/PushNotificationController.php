<?php

namespace App\Http\Controllers;

use App\DataTables\PushNotificationDataTable;
use App\Http\Requests\StripTagRequest as Request;
use App\Http\Resources\VideoResource;
use App\Models\Enums\PushNotificationAction;
use App\Models\PushNotification;
use App\Models\TestRecipient;
use App\Models\User;
use App\Models\Video;
use App\Services\PushNotificationService;
use App\Services\ValidationService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class PushNotificationController extends Controller
{
    public function __construct()
    {
        $this->authorizeMethod("test", "create", PushNotification::class);
        $this->authorizeResource(PushNotification::class, 'push_notification');
    }

    /**
     * Display a listing of the resource.
     *
     * @param PushNotificationDataTable $dataTable
     * @return mixed
     */
    public function index(PushNotificationDataTable $dataTable): mixed
    {
        return $dataTable->render('pages.notifications.index');
    }

    public function create(Request $request): View|Factory|\Illuminate\Database\Eloquent\Builder|JsonResponse|AnonymousResourceCollection|Application|Builder|null
    {
        $actions = PushNotificationAction::getAdminConfigurationActions();
        if ($request->ajax()) {
            $action = $request->get('action');
            if (!$action || !isset($actions[$action])) {
                return response()->json(['error' => __('notification.create.message.action_not_supported')], 400);
            }

            return match ($actions[$action]) {
                PushNotificationAction::Default => response()->json(),
                PushNotificationAction::Video => VideoResource::collection(self::getActionTarget(PushNotificationAction::Video)),
                default => response()->json(['error' => __('notification.create.message.action_not_supported')], 400),
            };
        }
        return view('pages.notifications.create', compact('actions'));
    }

    private static function getActionTarget(PushNotificationAction $action = PushNotificationAction::Default): Builder|null|\Illuminate\Database\Eloquent\Builder
    {
        return match ($action) {
            PushNotificationAction::Default => null,
            PushNotificationAction::Video => Video::active()
        };
    }

    public static function getActionTargetForValidation(Request $request = null): Builder|null|\Illuminate\Database\Eloquent\Builder
    {
        if (!$request) {
            $request = request();
        }
        $action = $request->input('action');
        $pushNotificationAction = PushNotificationAction::guess($action);
        return PushNotificationController::getActionTarget($pushNotificationAction);
    }

    public function store(Request $request, PushNotificationService $service): JsonResponse
    {
        return $this->sendPushNotification($request, $service);
    }

    public function test(Request $request, PushNotificationService $service): JsonResponse
    {
        return $this->sendPushNotification($request, $service, true);
    }

    private function sendPushNotification(Request $request, PushNotificationService $service, $testing = false): JsonResponse
    {
        if ($response = $this->validateRequest($request)) {
            return $response;
        }

        try {
            $pushNotification = $this->preparePushNotification($request, $service);

            // Before save, get change value (new) and original value (old) of push notification
            $changes = LoggerController::getChangedData($pushNotification);

            if (!$testing) {
                if (!$pushNotification->save()) {
                    throw new Exception("Failed to save item");
                }

                $recipients = User::all();

                $auditMessage = "audit_log.message.send_notification";

                $response = [
                    "success" => __("notification.create.message.success"),
                    "button" => __("notification.create.button.view_listing"),
                    "redirect" => route("notification.index")
                ];
            } else {
                $recipients = TestRecipient::getAllNotifiables();

                $auditMessage = "audit_log.message.send_notification_to_test_recipients";

                $response = [
                    "success" => __("notification.create.message.success_to_test_recipients"),
                    "button" => __("general.button.ok"),
                ];
            }

            $service->send($pushNotification->mapToPushNotification()->unsetChannels($testing ? 'database' : null), $recipients);

            // Log Audit
            LoggerController::log("push_notifications", $pushNotification, $auditMessage, $pushNotification->title, $changes);

            return response()->json($response);

        } catch (Exception $e) {

            catchException($e);
            return response()->json(["error" => __("notification.create.message.failed_to_send_notification")], 500);
        }
    }

    private function validateRequest(Request $request) : JsonResponse|null
    {
        $validator = ValidationService::getValidator("push_notification", "create", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }
        return null;
    }

    private function preparePushNotification($request, PushNotificationService $service): PushNotification
    {
        $pushNotification = [];
        $pushNotification['title'] = $request->get("title");
        $pushNotification['body'] = $request->get("message");

        if ($service->isImageEnabled() && $request->hasFile("image")) {
            $image = (new FileController)->uploadFile($request->file("image"), "push_notification");
            $pushNotification['image'] = $image["file_path"];
        } else {
            $pushNotification['image'] = "";
        }

        if ($service->isLargeIconEnabled() && $request->hasFile("icon")) {
            $largeIcon = (new FileController)->uploadFile($request->file("icon"), "push_notification");
            $pushNotification['icon'] = $largeIcon["file_path"];
        } else {
            $pushNotification['icon'] = "";
        }

        $pushNotification['sent_by'] = Auth::id();
        $pushNotification['sender_ip'] = request()->ip();

        $action = $request->get('action') ?? "Default";
        $pushNotificationAction = PushNotificationAction::guess($action);

        $actionTarget = $request->get('action_target');

        return (new PushNotification($pushNotification))->withAction($pushNotificationAction, item: $actionTarget);
    }
}
