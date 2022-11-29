<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StripTagRequest as Request;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BannerController extends Controller
{
    public function getBanners(Request $request): AnonymousResourceCollection
    {
        $banners = Banner::query()
            ->active()
            ->get();

        return BannerResource::collection($banners);
    }
}
