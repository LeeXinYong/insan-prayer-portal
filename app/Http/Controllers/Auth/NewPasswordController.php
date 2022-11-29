<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StripTagRequest as Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     *
     * @param Request $request
     * @param $token
     * @return View
     */
    public function create(Request $request, $token): View
    {
        return view("auth.reset-password", compact("request", "token"));
    }

    /**
     * Handle an incoming new password request.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            "token"    => "required",
            "email"    => "required|email|exists:users,email",
            "password" => ["required", "confirmed", Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise, we will parse the error and return the response.
        $status = Password::reset(
            $request->only("email", "password", "password_confirmation", "token"),
            function ($user) use ($request) {
                $user->forceFill([
                    "password" => bcrypt($request->password)
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
            ? response()->json([
                "message" => __("auth.reset_password.success_reset"),
                "button" => __("auth.reset_password.login"),
                "redirect" => route("login")
            ])
            : response()->json(["errors" => [["email" => __($status)]]], 422);
    }
}
