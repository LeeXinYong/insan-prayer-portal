<?php

namespace App\Exceptions\RoleModifiers;

use Exception;
use Throwable;

class MustHaveAtLeastOneRoleException extends Exception
{
    public function __construct($user, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->message = $message ?: "User {$user->name} must have at least one role";
    }
}
