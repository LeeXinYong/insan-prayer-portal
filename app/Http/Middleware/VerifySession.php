<?php

namespace App\Http\Middleware;

use App\Models\DeviceLogin;
use App\Models\SysParam;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifySession
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return RedirectResponse|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // If current user session has expired, logout user
        if(DeviceLogin::query()->where([["session_id", "=", $request->session()->getId()], ["session_expired_at", "!=", null]])->exists()) {
            Auth::guard("web")->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route("login")->with("error", __("layout.session.session_revoked_text"));
        }

        // If not allow concurrent login, check if user account's latest device session is not current session, set current session expired and logout user
        if(!(SysParam::get("web_portal_concurrent_login") ?? false) && DeviceLogin::query()->where([["user_id", "=", Auth::user()->id], ["session_expired_at", "=", null]])->orderByDesc("created_at")->first()?->session_id != $request->session()->getId()) {
            Auth::guard("web")->logout();
            DeviceLogin::query()->where("session_id", "=", $request->session()->getId())->update(["session_expired_at" => Carbon::now()]);
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route("login")->with("error", __("layout.session.session_concurrent_text"));
        }

        return $next($request);
    }
}
