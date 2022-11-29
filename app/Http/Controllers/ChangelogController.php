<?php

namespace App\Http\Controllers;

use App\Http\Requests\StripTagRequest as Request;
use App\Models\Changelog;
use App\Services\ValidationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ChangelogController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Changelog::class, "changelog");
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $changelogs = ChangeLog::query()
            ->select([
                "*",
                DB::raw("DATE_FORMAT(released_at, '%d %M %Y') as released_at"),
                DB::raw("SUBSTRING_INDEX(CONCAT(version,'.0'),'.',2) as main_version"),
            ])
            ->orderByRaw("INET_ATON(SUBSTRING_INDEX(CONCAT(version,'.0.0'),'.',3)) DESC")
            ->get()
            ->groupBy("main_version")
            ->map(function ($changelog, $main_version) {
                // Check if last changelog version is main version {x.x.0}
                if($changelog->last()->version == $main_version . ".0") {
                    return collect([
                        "main" => $changelog->last(),
                        "patch" => $changelog->slice(0, -1)
                    ]);
                } else {
                    return collect([
                        "main" => (new Changelog)
                            ->setAttribute("version", $main_version . ".0")
                            ->setAttribute("main_version", $main_version),
                        "patch" => $changelog
                    ]);
                }
            });

        return view("pages.changelog.index", compact("changelogs"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view("pages.changelog.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator("changelog", "create", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $changelog = new Changelog;
            $changelog->version = $request->get("version");
            $changelog->type = "feature";
            $changelog->released_by = $request->get("released_by");
            $changelog->released_at = Carbon::createFromFormat("j M Y", $request->get("released_at"))->toDateString();
            $changelog->description = implode("\n", $request->get("description"));
            $changelog->added_by = Auth::id();
            $changelog->added_ip = request()->ip();
            $changelog->updated_by = Auth::id();
            $changelog->updated_ip = request()->ip();

            // Before save, get change value (new) and original value (old) of changelog
            $changes = LoggerController::getChangedData($changelog);

            $changelog->save();

            // Log Audit
            LoggerController::log("changelogs", $changelog, "audit_log.message.create_changelog", $changelog->version, $changes);

            return response()->json([
                "success" => __("changelog.message.success_create"),
                "button" => __("changelog.button.view_listing"),
                "redirect" => route("system.log.changelog.index")
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param Changelog $changelog
     * @return View
     */
    public function edit(Request $request, Changelog $changelog): View
    {
        $changelog->description = explode("\n", $changelog->description);

        return view("pages.changelog.edit", compact("changelog"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Changelog $changelog
     * @return JsonResponse
     */
    public function update(Request $request, Changelog $changelog): JsonResponse
    {
        $validator = ValidationService::getValidator("changelog", "edit", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $changelog->description = implode("\n", $request->get("description"));
            $changelog->updated_by = Auth::id();
            $changelog->updated_ip =  request()->ip();

            // Before save, get change value (new) and original value (old) of changelog
            $changes = LoggerController::getChangedData($changelog);

            $changelog->save();

            // Log Audit
            LoggerController::log("changelogs", $changelog, "audit_log.message.update_changelog", $changelog->version, $changes);

            return response()->json([
                "success" => __("changelog.message.success_update"),
                "button" => __("changelog.button.view_listing"),
                "redirect" => route("system.log.changelog.index")
            ]);
        }
    }
}
