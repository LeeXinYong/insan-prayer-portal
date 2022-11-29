<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function authorizeMethod($method, $ability, $name, array $options = [])
    {
        if (!str_contains($name, '\\')){
            $name = "'$name'";
        }

        $middleware = "can:{$ability},{$name}";
        $this->middleware($middleware, $options)->only($method);
    }
}
