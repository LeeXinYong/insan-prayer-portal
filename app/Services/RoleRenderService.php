<?php

namespace App\Services;

use App\Core\Adapters\Theme;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class RoleRenderService
{
    /**
     * @param Collection<Role> $roles
     * @return string
     */
    public static function render(Collection $roles): string
    {
        return "<div class='d-flex flex-start gap-2'>" . implode("", array_map(fn($role) => DataTableRenderHelper::renderTextInitial($role['name'], $role['color']), $roles->toArray())) . "</div>";
    }
}
