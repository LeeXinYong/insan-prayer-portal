<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class LogApiRequestsAndResponses
{
    private array $except = [

    ];

    public static ?LoggerInterface $logger = null;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next): mixed
    {
        $route = $request->path();
        $args = $request->all();
        $excepts = $this->except[$route] ?? [];
        $loggedArgs = self::removeUnloggedParams($args, $excepts);

        $response = $next($request);
        static::$logger->info("API call to $route", ["request_ip" => request()->ip(), "args" => $loggedArgs, "response" => $response]);

        return $response;
    }



    private static function removeUnloggedParams(array $args, array $excepts = []): array
    {
        $excepts = array_merge($excepts, ["hashed_value"]);
        return array_reduce($excepts, function ($array, $except) {
            return self::removeKey($array, $except);
        }, $args);
    }

    private static function removeKey(array $array, string $key): array
    {
        if (array_key_exists($key, $array)) {
            unset($array[$key]);
            return $array;
        }

        $original = &$array;

        $parts = explode('.', $key);

        while (count($parts) > 1) {
            $part = array_shift($parts);

            if ($part === '*') {
                $array = array_map(function ($arr) use ($parts) {
                    return self::removeKey($arr, implode(".", $parts));
                }, $array);
                return $original;
            }

            if (isset($array[$part]) && is_array($array[$part])) {
                $array = &$array[$part];
            }
        }

        unset($array[array_shift($parts)]);

        return $original;
    }
}
