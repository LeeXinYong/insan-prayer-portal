<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StripTagRequest as Request;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsController extends Controller
{
    public function getNews (Request $request): AnonymousResourceCollection
    {
        $news = News::query()
            ->active()
            ->get();

        return NewsResource::collection($news);
    }
}
