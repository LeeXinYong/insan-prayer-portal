<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Http\Requests\StripTagRequest as Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function uploadFile($file, $type, $disk = "public"): array
    {
        $filename = $this->createFilename($file);
        $filepath = $file->storeAs("files/" . $type, $filename, $disk);
        return array(
            "file_name" => $filename,
            "file_path" => $filepath,
            "file_size" => $file->getSize(),
        );
    }

    public function createImageFromBase64($base64, $pdf_file, $file_category): array
    {
        $file_data = $base64;
        // check if object or string name
        if(is_object($pdf_file)) {
            $file_name = $this->createFilename($pdf_file, false).'.jpg'; // add extension;
        } else {
            $file_name = $pdf_file."_".Carbon::now()->timestamp.".jpg";
        }
        @list(, $file_data) = explode(',', $file_data);
        $decoded = base64_decode($file_data);
        $path = "files/".$file_category."/".$file_name;
        Storage::disk("public")->put($path, $decoded);

        return array(
            "file_name" => $file_name,
            "file_path" => $path,
            "file_size" => strlen($decoded),
        );
    }

    public function getFileViaLink(Request $request): Response|Application|ResponseFactory
    {
        try {
            $instance = app("App\\Models\\" . str_replace([" ", "_"], "", Str::title($request->file_module ?? "")))::find($request->module_id ?? "");
            $path = $instance->{$request->file_path_field ?? ""};
            $file = Storage::disk($request->disk ?? "public")->get($path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $name = ($instance->title ?? $instance->name ?? pathinfo($path, PATHINFO_FILENAME)) . ".$extension";

            return match ($extension) {
                "pdf" => response($file, 200)->header("Content-Type", "application/" . $extension)->header("Content-disposition","filename=\"$name\""),
                "jpg", "jpeg", "png", "gif" => response($file, 200)->header("Content-Type", "image/" . $extension)->header("Content-disposition","filename=\"$name\""),
                "svg" => response($file, 200)->header("Content-Type", "image/" . $extension . "+xml")->header("Content-disposition","filename=\"$name\""),
                "mp4" => response($file, 200)->header("Content-Type", "video/" . $extension)->header("Content-disposition","filename=\"$name\""),
                default => response($file, 200)->header("Content-Type", "application/octet-stream")->header("Content-disposition","filename=\"$name\""),
            };
        } catch (Exception) {
            abort(404);
        }
    }

    public function createFilename($file, $ext = true): string
    {
        $dt = "_" . Carbon::now()->timestamp;
        $file = $file->getClientOriginalName();
        $filename = cleanTitleToFilename(pathinfo($file, PATHINFO_FILENAME));
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        return ($ext) ? ($filename . $dt . "." . $extension) : ($filename . $dt);
    }

}
