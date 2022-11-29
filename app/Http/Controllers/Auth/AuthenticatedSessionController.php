<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\StripTagRequest as Request;
use App\Models\DeviceLogin;
use App\Models\User;
use App\Services\DeviceLoginService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Grosv\LaravelPasswordlessLogin\LoginUrl;
use App\Http\Controllers\LoggerController;
use App\Jobs\SendEmail;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return View
     */
    public function create(): View
    {
        return view("auth.login");
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $device = (new DeviceLoginService)->checkDevice();

        return response()->json(["redirect" => theme()->getPageUrl("")])->withCookie(cookie()->forever((Auth::user()->id ?? "") . "_device", $device->id ?? ""));
    }

    /**
     * Handle an incoming api authentication request.
     *
     * @param LoginRequest $request
     *
     * @return Response
     *
     * @throws ValidationException
     */
    public function apiStore(LoginRequest $request): Response
    {
        if (!Auth::attempt($request->only("email", "password"))) {
            throw ValidationException::withMessages([
                "email" => ["The provided credentials are incorrect"]
            ]);
        }

        $user = User::query()->where("email", $request->email ?? "")->first();
        return response($user);
    }

    /**
     * Verifies user token.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws ValidationException
     */
    public function apiVerifyToken(Request $request): Response
    {
        $request->validate([
            "api_token" => "required"
        ]);

        $user = User::query()->where("api_token", $request->api_token ?? "")->first();

        if(!$user){
            throw ValidationException::withMessages([
                "token" => ["Invalid token"]
            ]);
        }
        return response($user);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard("web")->logout();

        DeviceLogin::query()->where("session_id", "=", $request->session()->getId())->update(["session_expired_at" => Carbon::now()]);

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect("/");
    }

    /**
     * Generate Magic Link login.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function MagicLogin(Request $request): JsonResponse
    {
        $request->validate([
            "email" => "required"
        ]);

        /** @var User $user */
        $user = User::query()->where("email", $request->email ?? "")->first();
        if ($user) {
            // check if account active
            if (!($user->status ?? 1)) {
                throw ValidationException::withMessages([
                    "email" => __("auth.account_suspended"),
                ]);
            }

            $generator = new LoginUrl($user);
            $url = $generator->generate();

            // Send $url in an email or text message to your user
            $data = array(
                "user_name" => $user->name ?? "",
                "magic_link" => $url,
                "buttons" => [
                    ["url" => $url, "text" => __("auth.login.sign_in_now")],
                ]
            );
            SendEmail::dispatch($user, $data, "magic_link");

            // Log Audit
            LoggerController::log("login", $user, "audit_log.message.send_magic_link", $user->name ?? "");

            return response()->json(["redirect" => null, "message" => __("auth.login.we_have_sent_you_passwordless_login_link", ["email" => $request->email ?? ""])]);
        }

        throw ValidationException::withMessages([
            "email" => __("auth.failed"),
        ]);
    }

    /**
     * Auto Logout on Session Timeout.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     */
    public function SessionTimeoutLogout(Request $request): RedirectResponse
    {
        Auth::guard("web")->logout();

        DeviceLogin::query()->where("session_id", "=", $request->session()->getId())->update(["session_expired_at" => Carbon::now()]);

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $msg = "";
        if ($request->redir ?? false) {
            $msg = __("layout.timeout.logout_redirect_text");
        }

        return redirect()->route("login")->with("status", $msg);
    }
}
