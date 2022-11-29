<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class GuessModalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $modalName, $parameter)
    {
        if (!request()->route()->parameter($parameter) instanceof $modalName) {
            request()->route()->setParameter($parameter, $modalName::findOrFail(request()->route()->parameter($parameter)));
        }
        return $next($request);
    }
}
