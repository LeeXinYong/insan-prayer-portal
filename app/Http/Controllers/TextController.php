<?php

namespace App\Http\Controllers;

use App\Http\Requests\StripTagRequest as Request;

class TextController extends Controller
{
    public function getText(Request $request): string
    {
        return __($request->index ?? "");
    }
}
