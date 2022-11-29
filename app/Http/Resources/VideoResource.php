<?php

namespace App\Http\Resources;

use App\Http\Resources\Helpers\InterceptedJsonResource;
use Illuminate\Support\Facades\Storage;

class VideoResource extends InterceptedJsonResource
{
    public function getData($request):array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "video_type" => $this->video_type,
            "thumbnail_path" => isset($this->thumbnail_path) && Storage::disk("public")->exists($this->thumbnail_path)
                ? route("getFile", [
                    "file_module" => "video",
                    "module_id" => $this->id,
                    "file_path_field" => self::getLocaleField("thumbnail_path"),
                    "file_name" => strtolower(pathinfo($this->thumbnail_path, PATHINFO_EXTENSION))
                ])
                : asset(theme()->getDemo() . "/customize/media/error/no-thumb.png"),
            "file_path" => $this->video_type == "upload" && isset($this->file_path) && Storage::disk("public")->exists($this->file_path)
                ? route("getFile", [
                    "file_module" => "video",
                    "module_id" => $this->id,
                    "file_path_field" => self::getLocaleField("file_path"),
                    "file_name" => strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION))
                ])
                : "",
            "youtube_url" => $this->video_type == "youtube" ? $this->youtube_url : "",
            "order" => $this->order
        ];
    }
}
