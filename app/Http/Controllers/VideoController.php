<?php

namespace App\Http\Controllers;

use App\Http\Requests\StripTagRequest as Request;
use App\Models\Video;
use App\Services\ValidationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Alaouy\Youtube\Facades\Youtube;
use Illuminate\Support\Facades\Storage;
use App\DataTables\VideoDataTable;
use Illuminate\View\View;

class VideoController extends SortableController
{

    public function __construct()
    {
        parent::__construct();

        $this->authorizeResource(Video::class, "video");
    }

    /**
     * Display a listing of the resource.
     *
     * @param VideoDataTable $dataTable
     * @return mixed
     */
    public function index(VideoDataTable $dataTable): mixed
    {
        return $dataTable->render("pages.video.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view("pages.video.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator("video", "create", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else{
            $video = new Video;
            $video->title = $request->get("video_title");
            $video->video_type = $request->get("video_type");
            $video->duration = $request->get("duration");
            $video->status = $request->get("status") ?? 0;

            // Youtube video
            if($request->get("video_type") == "youtube"){
                $video->youtube_url = $request->get("youtube_url");
                $video->youtube_video_id = $request->get("youtube_video_id");
                $video->youtube_thumbnail_link = $request->get("youtube_thumbnail_link");
            }

            // Upload video
            if($request->hasFile("video_file") && $request->get("video_type") == "upload"){
                $video_file = (new FileController)->uploadFile($request->file("video_file"), "video");
                $video->file_name = $video_file["file_name"];
                $video->file_path = $video_file["file_path"];
                $video->file_size = $video_file["file_size"];
            }

            // upload thumbnail. manual or auto generated
            if($request->has("thumbnail_switch")) {
                $filename = ($request->get("video_type") == "upload") ? $request->file("video_file") : cleanTitleToFilename($request->get("title"));
                $thumbnail = (new FileController)->createImageFromBase64($request->get("auto_thumbnail"), $filename, "video_thumbnail");
            } else {
                $thumbnail = (new FileController)->uploadFile($request->file("manual_thumbnail"), "video_thumbnail");
            }
            $video->thumbnail_name = $thumbnail["file_name"];
            $video->thumbnail_path = $thumbnail["file_path"];
            $video->thumbnail_size = $thumbnail["file_size"];

            $video->added_by = Auth::user()->id;
            $video->added_ip = $request->ip();
            $video->updated_by = Auth::user()->id;
            $video->updated_ip = $request->ip();

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($video);

            $video->save();

            // Log Audit
            LoggerController::log("videos", $video, "audit_log.message.create_video", $video->title, $changes);

            return response()->json([
                "success" => __("video.message.success_create"),
                "button" => __("video.button.view_listing"),
                "redirect" => route("video.index")
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param Video $video
     * @return View
     */
    public function edit(Request $request, Video $video): View
    {
        return view("pages.video.edit", ["video" => $video]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Video $video
     * @return JsonResponse
     */
    public function update(Request $request, Video $video): JsonResponse
    {
        $validator = ValidationService::getValidator("video", "edit", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $video->video_type = $request->get("video_type");
            $video->title = $request->get("title");
            $video->duration = $request->get("duration");
            $video->status = $request->get("status") ?? 0;

            // upload thumbnail. manual or auto generated
            if($request->get("new_video_upload") == "1") {
                // Clear previous video upload details
                if (!empty($video->file_path) && Storage::disk("public")->exists($video->file_path)) {
                    Storage::disk("public")->delete($video->file_path);
                }
                $video->file_name = null;
                $video->file_path = null;
                $video->file_size = null;
                $video->youtube_url = null;
                $video->youtube_video_id = null;
                $video->youtube_thumbnail_link = null;

                // Youtube video
                if($request->get("video_type") == "youtube"){
                    $video->youtube_url = $request->get("youtube_url");
                    $video->youtube_video_id = $request->get("youtube_video_id");
                    $video->youtube_thumbnail_link = $request->get("youtube_thumbnail_link");
                }

                // Upload video
                if($request->hasFile("video_file") && $request->get("video_type") == "upload"){
                    $video_file = (new FileController)->uploadFile($request->file("video_file"), "video");
                    $video->file_name = $video_file["file_name"];
                    $video->file_path = $video_file["file_path"];
                    $video->file_size = $video_file["file_size"];
                }

                if ($request->has("thumbnail_switch")) {
                    $filename = ($request->get("video_type") == "upload") ? $request->file("video_file") : cleanTitleToFilename($request->get("title"));
                    $thumbnail = (new FileController)->createImageFromBase64($request->get("auto_thumbnail"), $filename, "video_thumbnail");
                } else {
                    $thumbnail = (new FileController)->uploadFile($request->file("manual_thumbnail"), "video_thumbnail");
                }
                $video->thumbnail_name = $thumbnail["file_name"];
                $video->thumbnail_path = $thumbnail["file_path"];
                $video->thumbnail_size = $thumbnail["file_size"];
            } else if($request->hasFile("manual_thumbnail")) {
                $thumbnail = (new FileController)->uploadFile($request->file("manual_thumbnail"), "video_thumbnail");
                $video->thumbnail_name = $thumbnail["file_name"];
                $video->thumbnail_path = $thumbnail["file_path"];
                $video->thumbnail_size = $thumbnail["file_size"];
            }

            $video->updated_by = Auth::user()->id;
            $video->updated_ip = $request->ip();

            $video->save();

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($video);

            $video->save();

            // Log Audit
            LoggerController::log("videos", $video, "audit_log.message.update_video", $video->title, $changes);

            return response()->json([
                "success" => __("video.message.success_update"),
                "button" => __("video.button.view_listing"),
                "redirect" => route("video.index")
            ]);
        }
    }

    protected function getSortableItems(): iterable
    {
        return Video::query()->orderBy("order")
            ->active()
            ->get()
            ->map(function($model){
                $model->img_url = Storage::disk('public')->exists($model->thumbnail_path ?? "") ?
                    route("getFile", ["file_module" => "video", "module_id" => $model->id, "file_path_field" => "thumbnail_path", "file_name" => strtolower(pathinfo($model->thumbnail_path, PATHINFO_EXTENSION))]) :
                    url(theme()->getDemo() . "/customize/media/error/no-thumb.png");
                return $model;
            });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Video $video
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Request $request, Video $video): JsonResponse|RedirectResponse
    {
        // Check the delete request origin
        // If request input not empty and "_token" exists, means from form
        // Else, means from index data-destroy axios delete
        $request_type = !empty($request->all()) && isset($request->_token);

        // Delete from db
        if ($video->delete()) {
            // Delete video from storage
            Storage::disk("public")->delete($video->thumbnail_path);
            if ($video->video_type == 1) {
                Storage::disk("public")->delete($video->file_path);
            }

            // Delete app downloads/views record from db
            $video->downloads()->delete();

            // Log Audit
            LoggerController::log("video", $video, "audit_log.message.delete_video", $video->title, $video->toArray());


            if ($request_type) {
                return redirect(route("video.index"))->with("message", __("video.message.success_delete", ["title" => $video->title]));
            } else {
                return response()->json(["message" => [__("video.message.success_delete", ["title" => $video->title])]]);
            }
        } else {
            if ($request_type) {
                return redirect()->back()->withErrors(["error" => __("video.message.fail_delete")]);
            } else {
                return response()->json(["error" => [__("video.message.fail_delete")]], 422);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function fetchData(Request $request): JsonResponse
    {
        $youtube_url = $request->youtube_url ?? "";

        // use regex to get only the YouTube video ID
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube_url, $match);


        if (!empty($match)) {
            $youtube_id = $match[1];

            $video_data = json_decode(json_encode(Youtube::getVideoInfo($youtube_id)), true);

            $title = $video_data["snippet"]["title"];
            $thumb_url_hq = (!empty($video_data["snippet"]["thumbnails"]["high"])) ? $video_data["snippet"]["thumbnails"]["high"]["url"] : "";
            $duration = convertISO8601Duration($video_data["contentDetails"]["duration"]);
            $b64image = "data:image/png;base64," . base64_encode(file_get_contents($thumb_url_hq));

            return response()->json([
                "url" => $youtube_url,
                "id" => $youtube_id,
                "title" => $title,
                "duration" => $duration,
                "thumbnail" => [
                    "link" => $thumb_url_hq,
                    "base64" => $b64image
                ]
            ], 201);
        } else {
            return response()->json([
                "message" => __("video.message.fail_fetch_youtube_api_msg"),
            ], 401);
        }
    }
}
