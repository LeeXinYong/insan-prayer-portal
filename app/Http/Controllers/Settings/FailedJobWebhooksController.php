<?php

namespace App\Http\Controllers\Settings;

use App\Enums\DefaultRole;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LoggerController;
use App\Http\Requests\StripTagRequest as Request;
use App\DataTables\FailedJobWebhooksDataTable;
use App\Models\FailedJobWebhook;
use App\Models\SysParam;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Jobs\SendEmail;
use Illuminate\Support\Str;
use Spatie\WebhookServer\Exceptions\CouldNotCallWebhook;
use Spatie\WebhookServer\WebhookCall;
use App\Events\FailedJobWebhookEvent;
use Carbon\Carbon;
use App\Models\User;
use App\Models\EmailTemplate;

class FailedJobWebhooksController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(FailedJobWebhook::class, 'failed_job_webhook');
    }

    protected function resourceMethodsWithoutModels()
    {
        return [
            ...parent::resourceMethodsWithoutModels(),
            'send',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @param FailedJobWebhooksDataTable $dataTable
     * @return mixed
     */
    public function index(FailedJobWebhooksDataTable $dataTable): mixed
    {
        return $dataTable->render("pages.settings.failedjob_webhook.index");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator("settings.failed_job_webhook", "create", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $secretkey = Str::uuid()->toString(); //generate secret key
            $webhook = new FailedJobWebhook();
            $webhook->secret_key = $secretkey;
            $webhook->endpoint = $request->get('webhook_url');

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($webhook);

            $webhook->save();

            // Log Audit
            LoggerController::log("failed_job_webhooks", $webhook, "audit_log.message.create_failed_job_webhook", $webhook->endpoint, $changes);

            return response()->json([
                "message" => __("settings.failed_job_webhook.message.success_create")
            ]);
        }
    }

    /**
     * Trigger Webhook
     *
     * @param string $event , array $data, $webhook_id
     * @param array $data
     * @param null $webhook_id
     * @return void
     */
    public static function send(string $event, array $data, $webhook_id = null): void
    {
        $payload = ['event' => $event, 'data' => $data, "environment" => config('app.env'), "timestamp" => Carbon::now()->timestamp];

        if ($webhook_id != null) { // Send testing webhooks
            $endpoints = FailedJobWebhook::query()->where([['id', $webhook_id]])->get();
        } else {
            $endpoints = FailedJobWebhook::query()->get();
        }

        foreach ($endpoints as $endpoint) {
            $secret = $endpoint->secret_key;

            // webhook call
            try {
                WebhookCall::create()
                    ->url($endpoint->endpoint)
                    ->payload($payload)
                    ->useSecret($secret)
                    ->dispatch();

                self::touchLastCalled($endpoint->id);
            } catch (CouldNotCallWebhook $e) {
                Log::error($e);
            } catch (\Exception $exception) {
                logException($exception);
            }
        }
    }

    public static function touchLastCalled($webhook_id)
    {
        $webhook = FailedJobWebhook::find($webhook_id);
        if ($webhook) {
            $webhook->last_called = Carbon::now();
            $webhook->save();
        }
    }

    /**
     * Trigger Webhook with test data
     *
     * @param  FailedJobWebhook $failed_job_webhook
     *
     * @return JsonResponse
     */
    public function test(FailedJobWebhook $failed_job_webhook)
    {
        if (!$failed_job_webhook) {
            return response()->json(["message" => __('settings.failed_job_webhook.message.webhook_not_found')], 422);
        }

        $event = __('settings.failed_job_webhook.message.failed_job_webhook_test');

        $data = [
            "job_name" => "Test Job",
            "job_id" => "123",
            "payload" => json_encode(['id' => 123, "message" => "Failed Job A"], JSON_PRETTY_PRINT),
            "exception" => json_encode(["error" => "Error Message"], JSON_PRETTY_PRINT),
            "failed_at" => Carbon::now()->setTimezone(Auth::user()->timezone)->diffForHumans() . ' (' . Carbon::now()->setTimezone(Auth::user()->timezone)->format('d M Y, h:i A') . ')',
        ];

        event(new FailedJobWebhookEvent($event, $data, $failed_job_webhook->id));

        // Log Audit
        LoggerController::log("failed_job_webhooks", $failed_job_webhook, "audit_log.message.send_test_failed_job_webhook", $failed_job_webhook->endpoint);

        return response()->json([
            "message" => __("settings.failed_job_webhook.message.success_test_webhook"),
        ]);
    }

    /**
     * Refresh the Webhook secret key
     *
     * @param  FailedJobWebhook $failed_job_webhook
     *
     * @return JsonResponse
     */
    public function refreshSecretKey(FailedJobWebhook $failed_job_webhook): JsonResponse
    {
        $secretkey = Str::uuid()->toString(); //generate secret key

        if (!$failed_job_webhook) {
            return response()->json(["message" => __('settings.failed_job_webhook.message.webhook_not_found')], 422);
        }

        $failed_job_webhook->secret_key = $secretkey;

        // Before save, get change value (new) and original value (old) of banner
        $changes = LoggerController::getChangedData($failed_job_webhook);

        $failed_job_webhook->save();

        // Log Audit
        LoggerController::log("failed_job_webhooks", $failed_job_webhook, "audit_log.message.regenerate_secret_key_failed_job_webhook", $failed_job_webhook->endpoint, $changes);

        return response()->json([
            "message" => __("settings.failed_job_webhook.message.success_regenerate_secret_key"),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  FailedJobWebhook $failed_job_webhook
     *
     * @return JsonResponse
     */
    public function destroy(FailedJobWebhook $failed_job_webhook): JsonResponse
    {
        // Delete from db
        $failed_job_webhook->delete();

        // Log Audit
        LoggerController::log("failed_job_webhooks", $failed_job_webhook, "audit_log.message.delete_failed_job_webhook", $failed_job_webhook->endpoint, $failed_job_webhook->toArray());

        return response()->json([
            "message" => __("settings.failed_job_webhook.message.success_delete"),
        ]);
    }

    public static function failedJobAlert($event)
    {
        $job_name = str_replace('App\Jobs\\', "", $event->job->resolveName());
        $event_title = __('settings.failed_job_webhook.message.failed_job_alert');
        $payload = [
            "job_name" => $job_name,
            "job_id" => $event->job->getJobId(),
            "payload" => json_encode(json_decode($event->job->getRawBody()), JSON_PRETTY_PRINT),
            "exception" => $event->exception->getTraceAsString(),
            "failed_at" => Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->diffForHumans() . ' (' . Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->format('d M Y, h:i A') . ')'
        ];

        if (SysParam::get('failed_job_email_alert') ?? false) {
            // send alert emails
            $super_admins = User::query()->whereHas('roles', fn ($query) => $query->where('default_role', '=', DefaultRole::SUPER_ADMIN->value))->where('status', 1)->get();
            $email_template = EmailTemplate::where('code', 'failed_job_alert')->first();

            foreach ($super_admins as $user) {
                $data = array(
                    "subject" => str_replace('{{job_name}}', $job_name, $email_template->subject),
                    "user_name" => $user->name,
                    "environment" => config('app.env')
                );

                $data = array_merge($payload, $data);

                SendEmail::dispatch($user, $data, 'failed_job_alert');
            }
        }


        if (SysParam::get('failed_job_webhook_alert') ?? false) {
            // push webhook notification
            event(new FailedJobWebhookEvent($event_title, $payload));
        }
    }
}
