<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LoggerController;
use App\Http\Requests\StripTagRequest as Request;
use App\Jobs\SendEmail;
use App\Models\User;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return View
     */
    public function create(): View
    {
        return view("auth.forgot-password");
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     */
    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator( "user", "forgot_password", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $user = User::where("email", $request->email)->first();

            if(!$user->status) {
                return response()->json(["errors" => [[__("auth.reset_password.suspended_account")]]], 422);
            }
            $reset_token = Password::getRepository()->create($user);

            // SEND RESET PASS EMAIL
            $data = array(
                "user_name" => $user->name,
                "reset_link" => route("password.reset", ["token" => $reset_token]),
                "buttons" => [
                    ["url" => route("password.reset", ["token" => $reset_token]), "text" => "Reset Password"]
                ]
            );
            SendEmail::dispatch($user, $data, "forgot_password");

            // Log Audit
            LoggerController::log("users", $user, "audit_log.message.forgot_password", $user->name);

            return response()->json([
                "message" => __("auth.reset_password.success_request")
            ]);
        }
    }

    /**
     * Handle an incoming api password reset link request.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws ValidationException
     */
    public function apiStore(Request $request): Response
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::query()->where('email', $request->email)->first();

        if(!$user){
            throw ValidationException::withMessages([
                'email' => ['User with such email doesn\'t exist']
            ]);
        }

        return response('Password reset email successfully sent.');
    }
}
