<?php

namespace App\Http\Middleware;

use App\Services\APIHashService;
use Closure;
use Illuminate\Http\Request;

class VerifyAPIKey
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (APIHashService::checkHash($request)) {
            return $next($request);
        } else {
            return response()->json([
                "status" => 0,
                "error" => [
                    "message" => __("api_responses.general.please_try_again")
                ]
            ], 400);
        }

    }
}
