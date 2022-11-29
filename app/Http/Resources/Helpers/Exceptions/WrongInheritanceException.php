<?php

namespace App\Http\Resources\Helpers\Exceptions;

class WrongInheritanceException extends \Exception
{
    protected $message = "The resource class must extend the InterceptedJsonResource class.";
}
