<?php

namespace App\Http\Controllers;

use App\Core\Adapters\Menu;
use App\DataTables\BannersDataTable;
use App\Http\Requests\StripTagRequest as Request;
use App\Models\Banner;
use App\Models\User;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BannerController extends SortableController
{

    public function __construct()
    {
        parent::__construct();

        $this->authorizeResource(Banner::class, "banner");
    }

    /**
     * Display a listing of the resource.
     *
     * @param BannersDataTable $dataTable
     * @return mixed
     */
    public function index(BannersDataTable $dataTable): mixed
    {
        return $dataTable->render("pages.banner.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $content_info_alert = array_map(function ($alert) {
            return "<li>$alert</li>";
        }, __("banner.message.content_info_alert"));
        $content_info_alert = "<ul class='my-auto'>" . implode("", $content_info_alert) . "</ul>";
        return view("pages.banner.create", compact("content_info_alert"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator("banner", "create", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $banner = new Banner;
            $banner->title = $request->get("title");

            if($request->has("url_content_switch")) {
                $banner->url_content_flag = "url";
                $banner->url = $request->get("url");
            } else {
                $banner->url_content_flag = "content";
                $banner->content = $request->get("content");
            }

            $img = (new FileController)->uploadFile($request->file("banner"), "banner");
            $banner->banner_name = $img["file_name"];
            $banner->banner_path = $img["file_path"];
            $banner->banner_size = $img["file_size"];

            $banner->status = $request->status ?? 0;
            $banner->added_by = Auth::id();
            $banner->added_ip = request()->ip();
            $banner->updated_by = Auth::id();
            $banner->updated_ip = request()->ip();

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($banner);

            $banner->save();

            // Log Audit
           LoggerController::log("banners", $banner, "audit_log.message.create_banner", $banner->title, $changes);

            return response()->json([
                "success" => __("banner.message.success_create"),
                "button" => __("banner.button.view_listing"),
                "redirect" => route("banner.index")
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param Banner $banner
     * @return View
     */
    public function edit(Request $request, Banner $banner): View
    {
        $content_info_alert = array_map(function ($alert) {
            return "<li>$alert</li>";
        }, __("banner.message.content_info_alert"));
        $content_info_alert = "<ul class='my-auto'>" . implode("", $content_info_alert) . "</ul>";
        return view("pages.banner.edit", compact("banner", "content_info_alert"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Banner $banner
     * @return JsonResponse
     */
    public function update(Request $request, Banner $banner): JsonResponse
    {
        $validator = ValidationService::getValidator("banner", "edit", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $banner->title = $request->get("title");

            if($request->has("url_content_switch")) {
                $banner->url_content_flag = "url";
                $banner->url = $request->get("url");
            } else {
                $banner->url_content_flag = "content";
                $banner->content = $request->get("content");
            }

            if ($request->hasFile("banner")) {
                // Delete old banner image from storage
                Storage::disk("public")->delete($banner->banner_path);

                // Store new banner image to storage
                $img = (new FileController)->uploadFile($request->file("banner"), "banner");
                $banner->banner_name = $img["file_name"];
                $banner->banner_path = $img["file_path"];
                $banner->banner_size = $img["file_size"];
            }

            $banner->status = $request->status ?? 0;
            $banner->updated_by = Auth::id();
            $banner->updated_ip = request()->ip();

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($banner);

            $banner->save();

            // Log Audit
           LoggerController::log("banners", $banner, "audit_log.message.update_banner", $banner->title, $changes);

            return response()->json([
                "success" => __("banner.message.success_update"),
                "button" => __("banner.button.view_listing"),
                "redirect" => route("banner.index")
            ]);
        }
    }

    protected function getSortableItems(): iterable
    {
        return Banner::query()
            ->active()
            ->orderBy("order")
            ->get()
            ->map(function($model){
                $model->img_url = Storage::disk('public')->exists($model->banner_path ?? "") ?
                    route("getFile", ["file_module" => "banner", "module_id" => $model->id, "file_path_field" => "banner_path", "file_name" => strtolower(pathinfo($model->banner_path, PATHINFO_EXTENSION))]) :
                    url(theme()->getDemo() . "/customize/media/error/no-thumb.png");
                return $model;
            });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Banner $banner
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Request $request, Banner $banner): JsonResponse|RedirectResponse
    {
        // Check the delete request origin
        // If request input not empty and "_token" exists, means from form
        // Else, means from index data-destroy axios delete
        $request_type = !empty($request->all()) && isset($request->_token);

        // Delete from db
        if($banner->delete()) {
            // Delete banner image from storage
            Storage::disk("public")->delete($banner->banner_path);

            // Delete app downloads/views record from db
            $banner->downloads()->delete();

            // Log Audit
           LoggerController::log("banners", $banner, "audit_log.message.delete_banner", $banner->title, $banner->toArray());

            if ($request_type) {
                return redirect(route("banner.index"))->with("message", __("banner.message.success_delete", ["title" => $banner->title]));
            } else {
                return response()->json(["message" => [__("banner.message.success_delete", ["title" => $banner->title])]]);
            }
        } else {
            if ($request_type) {
                return redirect()->back()->withErrors(["error" => __("banner.message.fail_delete")]);
            } else {
                return response()->json(["error" => [__("banner.message.fail_delete")]], 422);
            }
        }
    }

}
