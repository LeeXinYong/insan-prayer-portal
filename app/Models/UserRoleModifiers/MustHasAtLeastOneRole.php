<?php

namespace App\Models\UserRoleModifiers;

use Spatie\Permission\Models\Role;

interface MustHasAtLeastOneRole
{
    function defaultRole(): Role;
}
