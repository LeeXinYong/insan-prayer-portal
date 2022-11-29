<?php

namespace App\Http\Controllers;

use App\DataTables\TestRecipientDataTable;
use App\Models\TestRecipient;
use App\Http\Requests\StripTagRequest as Request;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TestRecipientController extends Controller
{

    public function __construct()
    {
        $this->authorizeMethod('index', 'viewAny', TestRecipient::class);
        $this->authorizeMethod('update', 'update', TestRecipient::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @param TestRecipientDataTable $dataTable
     * @return mixed
     */
    public function index(TestRecipientDataTable $dataTable): mixed
    {
        return $dataTable->render('pages.notifications.test_recipients.index');
    }

    public function update(Request $request, User $user): JsonResponse
    {
        try {
            if ($request->get('make_test_recipient') == 1) {
                $testRecipient = $user->becomeTestRecipient();
                $message = __("notification.manage_test_recipients.become_a_test_recipient", ["user" => $user->name]);
                $auditMessage = "audit_log.message.become_a_test_recipient";
            } else {
                $testRecipient = $user->resignTestRecipient();
                $message = __("notification.manage_test_recipients.remove_from_test_recipients", ["user" => $user->name]);
                $auditMessage = "audit_log.message.resign_as_test_recipient";
            }

            LoggerController::log("test_recipients", $testRecipient, $auditMessage, $testRecipient->notifiable->name);

            return response()->json(["message" => $message]);
        } catch (Exception $e) {
            catchException($e);
            return response()->json(["error" => __("general.message.please_try_again")], 500);
        }
    }
}
