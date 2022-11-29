<?php

namespace App\Http\Resources;

use App\Http\Resources\Helpers\InterceptedJsonResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class NewsResource extends InterceptedJsonResource
{
    public function getData($request):array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "thumbnail_path" => isset($this->thumbnail_path) && Storage::disk("public")->exists($this->thumbnail_path)
                ? route("getFile", [
                    "file_module" => "thumbnail",
                    "module_id" => $this->id,
                    "file_path_field" => self::getLocaleField("thumbnail_path"),
                    "file_name" => strtolower(pathinfo($this->thumbnail_path, PATHINFO_EXTENSION))
                ])
                : asset(theme()->getDemo() . "/customize/media/error/no-thumb.png"),
            "type" => $this->url_content_flag,
            "url" => $this->url_content_flag == "url" ? $this->url : null,
            "content" => $this->url_content_flag == "content" ? $this->content : null,
            "order" => $this->order
        ];
    }
}
