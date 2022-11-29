<?php

namespace App\Http\Resources;

use App\Http\Resources\Helpers\InterceptedJsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends InterceptedJsonResource
{
    public function getData($request):array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "banner_path" => isset($this->banner_path) && Storage::disk("public")->exists($this->banner_path)
                ? route("getFile", [
                    "file_module" => "banner",
                    "module_id" => $this->id,
                    "file_path_field" => self::getLocaleField("banner_path"),
                    "file_name" => strtolower(pathinfo($this->banner_path, PATHINFO_EXTENSION))
                ])
                : asset(theme()->getDemo() . "/customize/media/error/no-thumb.png"),
            "type" => $this->url_content_flag,
            "url" => $this->url_content_flag == "url" ? $this->url : null,
            "content" => $this->url_content_flag == "content" ? $this->content : null,
            "order" => $this->order
        ];
    }
}
