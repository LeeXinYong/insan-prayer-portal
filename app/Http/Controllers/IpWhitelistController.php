<?php

namespace App\Http\Controllers;

use App\DataTables\IpWhitelistsDataTable;
use App\Models\IpWhitelist;
use App\Models\User;
use App\Http\Requests\StripTagRequest as Request;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IpWhitelistController extends SortableController
{    
    public function __construct()
    {
        parent::__construct();

        $this->authorizeResource(IpWhitelist::class, "ip_whitelist");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IpWhitelistsDataTable $dataTable): mixed
    {
        return $dataTable->render("pages.ip_whitelist.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {

        return view("pages.ip_whitelist.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator("ip_whitelist", "create", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $ip_whitelist = new IpWhitelist;
            $ip_whitelist->ip_address = $request->get("ip_address");
            $ip_whitelist->ip_description = $request->get("ip_description");

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($ip_whitelist);

            $ip_whitelist->save();

            // Log Audit
           LoggerController::log("ip_whitelists", $ip_whitelist, "audit_log.message.create_ip_whitelist", $ip_whitelist->ip_whitelist_id, $changes);

            return response()->json([
                "success" => __("ip_whitelist.message.success_create"),
                "button" => __("ip_whitelist.button.view_listing"),
                "redirect" => route("ip_whitelist.index")
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
    public function edit(Request $request, IpWhitelist $ip_whitelist): View
    {
        return view("pages.ip_whitelist.edit", compact("ip_whitelist"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IpWhitelist $ip_whitelist): JsonResponse
    {
        $validator = ValidationService::getValidator("ip_whitelist", "edit", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $ip_whitelist->ip_description = $request->get("ip_description");

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($ip_whitelist);

            $ip_whitelist->save();

            // Log Audit
           LoggerController::log("ip_whitelists", $ip_whitelist, "audit_log.message.update_ip_whitelist", $ip_whitelist->consumer_id, $changes);

            return response()->json([
                "success" => __("ip_whitelist.message.success_update"),
                "button" => __("ip_whitelist.button.view_listing"),
                "redirect" => route("ip_whitelist.index")
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, IpWhitelist $ip_whitelist): JsonResponse|RedirectResponse
    {
        // Check the delete request origin
        // If request input not empty and "_token" exists, means from form
        // Else, means from index data-destroy axios delete
        $request_type = !empty($request->all()) && isset($request->_token);

        // Delete from db
        if($ip_whitelist->delete()) {
            // Log Audit
           LoggerController::log("ip_whitelists", $ip_whitelist, "audit_log.message.delete_ip_whitelist", $ip_whitelist->consumer_id, $ip_whitelist->toArray());

            if ($request_type) {
                return redirect(route("ip_whitelist.index"))->with("message", __("ip_whitelist.message.success_delete", ["title" => $ip_whitelist->consumer_id]));
            } else {
                return response()->json(["message" => [__("ip_whitelist.message.success_delete", ["title" => $ip_whitelist->consumer_id])]]);
            }
        } else {
            if ($request_type) {
                return redirect()->back()->withErrors(["error" => __("ip_whitelist.message.fail_delete")]);
            } else {
                return response()->json(["error" => [__("ip_whitelist.message.fail_delete")]], 422);
            }
        }
    }
}
