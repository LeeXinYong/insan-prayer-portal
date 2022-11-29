<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StripTagRequest as Request;
use App\Http\Resources\BrochureResource;
use App\Models\Brochure;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BrochureController extends Controller
{
    public function getBrochures (Request $request): AnonymousResourceCollection
    {
        $brochures = Brochure::query()
            ->active()
            ->get();

        return BrochureResource::collection($brochures);
    }
}
