<?php

namespace App\Http\Controllers;

use App\DataTables\PrayerTimesDataTable;
use App\Models\PrayerTime;
use App\Models\User;
use App\Http\Requests\StripTagRequest as Request;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PrayerTimeController extends SortableController
{
    public function __construct()
    {
        parent::__construct();

        $this->authorizeResource(PrayerTime::class, "prayer_time");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PrayerTimesDataTable $dataTable): mixed
    {
        return $dataTable->render("pages.prayer_time.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, PrayerTime $prayer_time): View
    {
        return view("pages.prayer_time.edit", compact("prayer_time"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PrayerTime $prayer_time): JsonResponse
    {
        $validator = ValidationService::getValidator("prayer_time", "edit", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $prayer_time->imsak = $request->get("imsak");
            $prayer_time->fajr = $request->get("fajr");
            $prayer_time->syuruk = $request->get("syuruk");
            $prayer_time->dhuhr = $request->get("dhuhr");
            $prayer_time->asr = $request->get("asr");
            $prayer_time->maghrib = $request->get("maghrib");
            $prayer_time->isha = $request->get("isha");

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($prayer_time);

            $prayer_time->save();

            // Log Audit
           LoggerController::log("prayer_time", $prayer_time, "audit_log.message.update_prayer_time", $prayer_time->title, $changes);

            return response()->json([
                "success" => __("prayer_time.message.success_update"),
                "button" => __("prayer_time.button.view_listing"),
                "redirect" => route("prayer_time.index")
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
