<?php

namespace App\Http\Controllers;

use App\DataTables\NewsDataTable;
use App\Http\Requests\StripTagRequest as Request;
use App\Models\News;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class NewsController extends SortableController
{
    public function __construct()
    {
        parent::__construct();

        $this->authorizeResource(News::class, "news");
    }

    /**
     * Display a listing of the resource.
     *
     * @param NewsDataTable $dataTable
     * @return mixed
     */
    public function index(NewsDataTable $dataTable): mixed
    {
        return $dataTable->render("pages.news.index");
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
        }, __("news.message.content_info_alert"));
        $content_info_alert = "<ul class='my-auto'>" . implode("", $content_info_alert) . "</ul>";
        return view("pages.news.create", compact("content_info_alert"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator("news", "create", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $news = new News;
            $news->title = $request->get("title");

            if($request->has("url_content_switch")) {
                $news->url_content_flag = "url";
                $news->url = $request->get("url");
            } else {
                $news->url_content_flag = "content";
                $news->content = $request->get("content");
            }

            $img = (new FileController)->uploadFile($request->file("thumbnail"), "news");
            $news->thumbnail_name = $img["file_name"];
            $news->thumbnail_path = $img["file_path"];
            $news->thumbnail_size = $img["file_size"];

            $news->status = $request->status ?? 0;
            $news->added_by = Auth::id();
            $news->added_ip = request()->ip();
            $news->updated_by = Auth::id();
            $news->updated_ip = request()->ip();

            // Before save, get change value (new) and original value (old) of news
            $changes = LoggerController::getChangedData($news);

            $news->save();

            // Log Audit
            LoggerController::log("news", $news, "audit_log.message.create_news", $news->title, $changes);

            return response()->json([
                "success" => __("news.message.success_create"),
                "button" => __("news.button.view_listing"),
                "redirect" => route("news.index")
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param News $news
     * @return View
     */
    public function edit(Request $request, News $news): View
    {
        $content_info_alert = array_map(function ($alert) {
            return "<li>$alert</li>";
        }, __("news.message.content_info_alert"));
        $content_info_alert = "<ul class='my-auto'>" . implode("", $content_info_alert) . "</ul>";
        return view("pages.news.edit", compact("news", "content_info_alert"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param News $news
     * @return JsonResponse
     */
    public function update(Request $request, News $news): JsonResponse
    {
        $validator = ValidationService::getValidator("news", "edit", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $news->title = $request->get("title");

            if($request->has("url_content_switch")) {
                $news->url_content_flag = "url";
                $news->url = $request->get("url");
            } else {
                $news->url_content_flag = "content";
                $news->content = $request->get("content");
            }

            if ($request->hasFile("thumbnail")) {
                // Delete old news thumbnail from storage
                Storage::disk("public")->delete($news->thumbnail_path);

                // Store new news thumbnail to storage
                $img = (new FileController)->uploadFile($request->file("thumbnail"), "news");
                $news->thumbnail_name = $img["file_name"];
                $news->thumbnail_path = $img["file_path"];
                $news->thumbnail_size = $img["file_size"];
            }

            $news->status = $request->status ?? 0;
            $news->updated_by = Auth::id();
            $news->updated_ip = request()->ip();

            // Before save, get change value (new) and original value (old) of news
            $changes = LoggerController::getChangedData($news);

            $news->save();

            // Log Audit
            LoggerController::log("news", $news, "audit_log.message.update_news", $news->title, $changes);

            return response()->json([
                "success" => __("news.message.success_update"),
                "button" => __("news.button.view_listing"),
                "redirect" => route("news.index")
            ]);
        }
    }

    protected function getSortableItems(): iterable
    {
        return News::query()
            ->active()
            ->orderBy("order")
            ->get()
            ->map(function($model){
                $model->img_url = Storage::disk('public')->exists($model->thumbnail_path ?? "") ?
                    route("getFile", ["file_module" => "news", "module_id" => $model->id, "file_path_field" => "thumbnail_path", "file_name" => strtolower(pathinfo($model->thumbnail_path, PATHINFO_EXTENSION))]) :
                    url(theme()->getDemo() . "/customize/media/error/no-thumb.png");
                return $model;
            });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param News $news
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Request $request, News $news): JsonResponse|RedirectResponse
    {
        // Check the delete request origin
        // If request input not empty and "_token" exists, means from form
        // Else, means from index data-destroy axios delete
        $request_type = !empty($request->all()) && isset($request->_token);

        // Delete from db
        if($news->delete()) {
            // Delete news thumbnail from storage
            Storage::disk("public")->delete($news->thumbnail_path);

            // Delete app downloads/views record from db
            $news->downloads()->delete();

            // Log Audit
            LoggerController::log("news", $news, "audit_log.message.delete_news", $news->title, $news->toArray());

            if ($request_type) {
                return redirect(route("news.index"))->with("message", __("news.message.success_delete", ["title" => $news->title]));
            } else {
                return response()->json(["message" => [__("news.message.success_delete", ["title" => $news->title])]]);
            }
        } else {
            if ($request_type) {
            return redirect()->back()->withErrors(["error" => __("news.message.fail_delete")]);
            } else {
                return response()->json(["error" => [__("news.message.fail_delete")]], 422);
            }
        }
    }
}
