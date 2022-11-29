<?php

namespace App\Services;

use Illuminate\Http\Request;

class APIHashService
{
    /**
     * Check API request data match with hashed value.
     *
     * @param Request $request
     * @return bool
     */
    public static function checkHash(Request $request): bool
    {
        $content = json_decode($request->getContent(), true);
        $data = $request->except(["hashed_value"]);
        if($data != null && is_array($data) && $content != null && is_array($content)) {
            array_walk($content, function ($value, $key) use (&$data) {
                if ($value == "" || $value == null) {
                    $data[$key] = $value;
                }
            });
        }
        $hashed_expected = hash_hmac("sha256", json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), config("app.api_key"));
        $hashed_value = $request->hashed_value ?? "";

        return hash_equals($hashed_expected, $hashed_value);
    }


    /**
     * Get API request data with hashed value.
     *
     * @param Request $request
     * @return array
     */
    public static function getHash(Request $request): array
    {
        $content = json_decode($request->getContent(), true);
        $data = $request->except(["hashed_value"]);
        if ($data != null && is_array($data) && $content != null && is_array($content)) {
            array_walk($content, function($value, $key) use (&$data) {
                if($value == "" || $value == null) {
                    $data[$key] = $value;
                }
            });
        }
        $hashed_expected = hash_hmac("sha256", json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), config("app.api_key"));

        return [
            "data" => $data,
            "hashed" => $hashed_expected
        ];
    }
}
