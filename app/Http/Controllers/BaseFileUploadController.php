<?php

namespace App\Http\Controllers;

use App\Http\Middleware\GuessModalMiddleware;
use App\Http\Requests\StripTagRequest as Request;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\Services\DataTable;

class BaseFileUploadController extends SortableController
{
    protected DataTable $dataTable;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct($dataTable = null)
    {
        parent::__construct();
        if(isset($dataTable) && $dataTable instanceof DataTable) {
            $this->dataTable = $dataTable;
        } else {
            $this->dataTable = $this->getBaseModelDataTable();
        }
        $this->middleware(GuessModalMiddleware::class.":".$this->getBaseModel()::class.",".$this->getBaseModelName())->only(["edit", "update", "destroy"]);
        $this->authorizeResource($this->getBaseModel()::class, $this->getBaseModelName());
    }

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index(): mixed
    {
        $model = [
            "class" => $this->getBaseModel(),
            "name" => $this->getBaseModelName()
        ];
        return $this->dataTable->render("pages." . $this->getBaseModelName() . ".index", compact("model"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $model = [
            "class" => $this->getBaseModel(),
            "name" => $this->getBaseModelName()
        ];
        return view("pages." . $this->getBaseModelName() . ".create", compact("model"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::getValidator($this->getBaseModelName(), "create", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $model = new ($this->getBaseModel());
            $model->title = $request->get("title");

            $pdf = (new FileController)->uploadFile($request->file("pdf_file"), $this->getBaseModelName() . "_file");
            $model->file_name = $pdf["file_name"];
            $model->file_path = $pdf["file_path"];
            $model->file_size = $pdf["file_size"];

            if($request->has("thumbnail_switch")) {
                $thumbnail = (new FileController)->createImageFromBase64($request->get("auto_thumbnail"), $request->file("pdf_file"), $this->getBaseModelName() . "_thumbnail");
            } else {
                $thumbnail = (new FileController)->uploadFile($request->file("manual_thumbnail"), $this->getBaseModelName() . "_thumbnail");
            }
            $model->thumbnail_name = $thumbnail["file_name"];
            $model->thumbnail_path = $thumbnail["file_path"];
            $model->thumbnail_size = $thumbnail["file_size"];

            $model->status = $request->status ?? 0;
            $model->added_by = Auth::id();
            $model->added_ip = request()->ip();
            $model->updated_by = Auth::id();
            $model->updated_ip = request()->ip();

            // Before save, get change value (new) and original value (old) of model
            $changes = LoggerController::getChangedData($model);

            $model->save();

            // Log Audit
            LoggerController::log($model->getTable(), $model, "audit_log.message.create_" . $this->getBaseModelName(), $model->title, $changes);

            return response()->json([
                "success" => __($this->getBaseModelName() . ".message.success_create"),
                "button" => __($this->getBaseModelName() . ".button.view_listing"),
                "redirect" => route($this->getBaseModelName() . ".index")
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param $model
     * @return View
     */
    public function edit(Request $request, $model): View
    {
        $model = [
            "class" => $this->getBaseModel(),
            "name" => $this->getBaseModelName(),
            "instance" => $model
        ];
        return view("pages." . $this->getBaseModelName() . ".edit", compact("model"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $model
     * @return JsonResponse
     */
    public function update(Request $request, $model): JsonResponse
    {
        $validator = ValidationService::getValidator($this->getBaseModelName(), "edit", request: $request);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        } else {
            $model->title = $request->get("title");

            if($request->hasFile("pdf_file")) {
                // delete old pdf file and thumbnail from storage
                Storage::disk("public")->delete($model->file_path);
                Storage::disk("public")->delete($model->thumbnail_path);

                // store new pdf file and thumbnail to storage
                $pdf = (new FileController)->uploadFile($request->file("pdf_file"), $this->getBaseModelName() . "_file");
                $model->file_name = $pdf["file_name"];
                $model->file_path = $pdf["file_path"];
                $model->file_size = $pdf["file_size"];

                if ($request->has("thumbnail_switch")) {
                    $thumbnail = (new FileController)->createImageFromBase64($request->get("auto_thumbnail"), $request->file("pdf_file"), $this->getBaseModelName() . "_thumbnail");
                } else {
                    $thumbnail = (new FileController)->uploadFile($request->file("manual_thumbnail"), $this->getBaseModelName() . "_thumbnail");
                }
                $model->thumbnail_name = $thumbnail["file_name"];
                $model->thumbnail_path = $thumbnail["file_path"];
                $model->thumbnail_size = $thumbnail["file_size"];
            } else if($request->hasFile("manual_thumbnail")) {
                // delete old thumbnail
                Storage::disk("public")->delete($model->thumbnail_path);

                $thumbnail = (new FileController)->uploadFile($request->file("manual_thumbnail"), $this->getBaseModelName() . "_thumbnail");
                $model->thumbnail_name = $thumbnail["file_name"];
                $model->thumbnail_path = $thumbnail["file_path"];
                $model->thumbnail_size = $thumbnail["file_size"];
            }

            $model->status = $request->status ?? 0;
            $model->updated_by = Auth::id();
            $model->updated_ip = request()->ip();

            // Before save, get change value (new) and original value (old) of banner
            $changes = LoggerController::getChangedData($model);

            $model->save();

            // Log Audit
            LoggerController::log($model->getTable(), $model, "audit_log.message.update_" . $this->getBaseModelName(), $model->title, $changes);

            return response()->json([
                "success" => __($this->getBaseModelName() . ".message.success_update"),
                "button" => __($this->getBaseModelName() . ".button.view_listing"),
                "redirect" => route($this->getBaseModelName() . ".index")
            ]);
        }
    }

    protected function getSortableItems(): iterable
    {
        return $this->getBaseModel()::where("status", 1)
            ->orderBy("order")
            ->get()
            ->map(function($model){
                $model->img_url = Storage::disk('public')->exists($model->thumbnail_path ?? "") ?
                    route("getFile", ["file_module" => $this->getBaseModelName(), "module_id" => $model->id, "file_path_field" => "thumbnail_path", "file_name" => strtolower(pathinfo($model->thumbnail_path, PATHINFO_EXTENSION))]) :
                    url(theme()->getDemo() . "/customize/media/error/no-thumb.png");

                return $model;
            });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $model
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Request $request, $model): JsonResponse|RedirectResponse
    {
        // Check the delete request origin
        // If request input not empty and "_token" exists, means from form
        // Else, means from index data-destroy axios delete
        $request_type = !empty($request->all()) && isset($request->_token);

        // Delete from db
        if ($model->delete()) {
            // delete pdf file and thumbnail from storage
            Storage::disk("public")->delete($model->file_path);
            Storage::disk("public")->delete($model->thumbnail_path);

            if (method_exists($model, "downloads")) {
                // Delete app downloads/views record from db
                $model->downloads()->delete();
            }

            // Log Audit
            LoggerController::log($model->getTable(), $model, "audit_log.message.delete_" . $this->getBaseModelName(), $model->title, $model->toArray());

            if ($request_type) {
                return redirect(route($this->getBaseModelName() . ".index"))->with("message", __($this->getBaseModelName() . ".message.success_delete", ["title" => $model->title]));
            } else {
                return response()->json(["message" => [__($this->getBaseModelName() . ".message.success_delete", ["title" => $model->title])]]);
            }
        } else {
            if ($request_type) {
                return redirect()->back()->withErrors(["error" => __($this->getBaseModelName() . ".message.fail_delete")]);
            } else {
                return response()->json(["error" => [__($this->getBaseModelName() . ".message.fail_delete")]], 422);
            }
        }
    }
}
