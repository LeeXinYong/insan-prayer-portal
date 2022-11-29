<?php

namespace App\Http\Controllers;

use App\Http\Requests\StripTagRequest as Request;
use App\Models\Timezone;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{

    /**
     * Function to get the timezone list of selected country
     *
     * @param Request $request
     * @return JsonResponse
     */

    public function getCountryTimezone(Request $request): JsonResponse
    {
        if(isset($request->country_id)) {
            return response()->json(["timezones" => Timezone::query()->where("country_id", $request->country_id)->get()->sortBy("timezone_name")->values()->all()]);
        } else {
            return response()->json(["errors" => "Failed to get data."], 422);
        }
    }
}
