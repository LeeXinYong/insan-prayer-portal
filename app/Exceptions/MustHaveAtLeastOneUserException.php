<?php

namespace App\Exceptions;

use Exception;
use Spatie\Permission\Models\Role;
use Throwable;

class MustHaveAtLeastOneUserException extends Exception
{
    public function __construct(Role $role, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->message = $message ?: "This user is currently assigned to role \"{$role->name}\", and it must have at least one user.";
    }
}
