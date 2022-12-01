<?php

namespace App\Http\Controllers;

use App\DataTables\ZonesDataTable;
use App\Models\Zone;
use App\Models\State;
use App\Models\User;
use App\Http\Requests\StripTagRequest as Request;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ZoneController extends SortableController
{
    public function __construct()
    {
        parent::__construct();

        $this->authorizeResource(Zone::class, "zone");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ZonesDataTable $dataTable): mixed
    {
        return $dataTable->render("pages.zone.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $states = State::query()->orderBy("name")->get();

        return view("pages.zone.create", compact("states"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator("zone", "create", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $zone = new Zone;
            $zone->zone_id = $request->get("zone_id");
            $zone->name = $request->get("name");
            $zone->state_id = $request->get("state_id");

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($zone);

            $zone->save();

            // Log Audit
           LoggerController::log("zones", $zone, "audit_log.message.create_banner", $zone->zone_id, $changes);

            return response()->json([
                "success" => __("zone.message.success_create"),
                "button" => __("zone.button.view_listing"),
                "redirect" => route("zone.index")
            ]);
        }
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
    public function edit(Request $request, Zone $zone): View
    {
        $states = State::query()->orderBy("name")->get();

        return view("pages.zone.edit", compact("zone", "states"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Zone $zone): JsonResponse
    {
        $validator = ValidationService::getValidator("zone", "edit", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $zone->name = $request->get("name");
            $zone->state_id = $request->get("state_id");

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($zone);

            $zone->save();

            // Log Audit
           LoggerController::log("zones", $zone, "audit_log.message.update_zone", $zone->zone_id, $changes);

            return response()->json([
                "success" => __("zone.message.success_update"),
                "button" => __("zone.button.view_listing"),
                "redirect" => route("zone.index")
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Zone $zone): JsonResponse|RedirectResponse
    {
        // Check the delete request origin
        // If request input not empty and "_token" exists, means from form
        // Else, means from index data-destroy axios delete
        $request_type = !empty($request->all()) && isset($request->_token);

        // Delete from db
        if($zone->delete()) {
            // Log Audit
           LoggerController::log("zones", $zone, "audit_log.message.delete_zone", $zone->zone_id, $zone->toArray());

            if ($request_type) {
                return redirect(route("zone.index"))->with("message", __("zone.message.success_delete", ["title" => $zone->zone_id]));
            } else {
                return response()->json(["message" => [__("zone.message.success_delete", ["title" => $zone->zone_id])]]);
            }
        } else {
            if ($request_type) {
                return redirect()->back()->withErrors(["error" => __("zone.message.fail_delete")]);
            } else {
                return response()->json(["error" => [__("zone.message.fail_delete")]], 422);
            }
        }
    }
}
