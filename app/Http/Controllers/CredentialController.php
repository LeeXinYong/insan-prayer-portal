<?php

namespace App\Http\Controllers;

use App\DataTables\CredentialsDataTable;
use App\Models\Credential;
use App\Models\User;
use App\Http\Requests\StripTagRequest as Request;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CredentialController extends SortableController
{
    public function __construct()
    {
        parent::__construct();

        $this->authorizeResource(Credential::class, "credential");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CredentialsDataTable $dataTable): mixed
    {
        return $dataTable->render("pages.credential.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {

        return view("pages.credential.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator("credential", "create", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $credential = new Credential;
            $credential->consumer_id = $request->get("consumer_id");
            $credential->signature = $request->get("signature");

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($credential);

            $credential->save();

            // Log Audit
           LoggerController::log("credentials", $credential, "audit_log.message.create_credential", $credential->credential_id, $changes);

            return response()->json([
                "success" => __("credential.message.success_create"),
                "button" => __("credential.button.view_listing"),
                "redirect" => route("credential.index")
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
    public function edit(Request $request, Credential $credential): View
    {
        return view("pages.credential.edit", compact("credential"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Credential $credential): JsonResponse
    {
        $validator = ValidationService::getValidator("credential", "edit", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $credential->signature = $request->get("signature");

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($credential);

            $credential->save();

            // Log Audit
           LoggerController::log("credentials", $credential, "audit_log.message.update_credential", $credential->consumer_id, $changes);

            return response()->json([
                "success" => __("credential.message.success_update"),
                "button" => __("credential.button.view_listing"),
                "redirect" => route("credential.index")
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Credential $credential): JsonResponse|RedirectResponse
    {
        // Check the delete request origin
        // If request input not empty and "_token" exists, means from form
        // Else, means from index data-destroy axios delete
        $request_type = !empty($request->all()) && isset($request->_token);

        // Delete from db
        if($credential->delete()) {
            // Log Audit
           LoggerController::log("credentials", $credential, "audit_log.message.delete_credential", $credential->consumer_id, $credential->toArray());

            if ($request_type) {
                return redirect(route("credential.index"))->with("message", __("credential.message.success_delete", ["title" => $credential->consumer_id]));
            } else {
                return response()->json(["message" => [__("credential.message.success_delete", ["title" => $credential->consumer_id])]]);
            }
        } else {
            if ($request_type) {
                return redirect()->back()->withErrors(["error" => __("credential.message.fail_delete")]);
            } else {
                return response()->json(["error" => [__("credential.message.fail_delete")]], 422);
            }
        }
    }
}
