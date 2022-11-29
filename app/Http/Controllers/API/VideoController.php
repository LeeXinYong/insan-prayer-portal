<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StripTagRequest as Request;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VideoController extends Controller
{
    public function getVideos (Request $request): AnonymousResourceCollection
    {
        $videos = Video::query()
            ->active()
            ->get();

        return VideoResource::collection($videos);
    }
}
