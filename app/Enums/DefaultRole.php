<?php

namespace App\Enums;

use App\Models\Enums\EnumTrait;
use Spatie\Permission\Models\Permission;

enum DefaultRole: string
{
    use EnumTrait;

    case SUPER_ADMIN = 'Super Admin';
    case ADMIN = 'Admin';
    case USER = 'User';

    public function getDefaultPermissions()
    {
        return match($this) {
            self::SUPER_ADMIN,
            self::ADMIN => Permission::all(),
            default => [],
        };
    }

    public function isProtected()
    {
        return match($this) {
            self::SUPER_ADMIN,
            self::ADMIN => true,
            default => false,
        };
    }

    public function hasUnlimitedAccess()
    {
        return match($this) {
            self::SUPER_ADMIN => true,
            default => false,
        };
    }

    public function getColor()
    {
        return match ($this) {
            self::SUPER_ADMIN => '#6732E7',
            self::ADMIN => '#EF3961',
            self::USER => '#FFC000',
            default => '#46C67E',
        };
    }

    public static function getDefault(): self
    {
        return self::USER;
    }
}
