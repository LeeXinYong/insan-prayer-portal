<?php

namespace App\Http\Resources\Helpers\Traits;

use App\Http\Resources\Helpers\Exceptions\WrongInheritanceException;
use App\Http\Resources\Helpers\InterceptedJsonResource;

trait VerifyInheritance
{
    public static function verifyInheritance()
    {
        if (!is_subclass_of(static::class, InterceptedJsonResource::class)) {
            throw new WrongInheritanceException;
        }
    }
}
