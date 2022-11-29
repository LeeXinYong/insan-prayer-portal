<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * This class's [permission_id , role_id] refer to role_has_permissions table, and utilise restrict on delete for these 2 columns to prevent deletion in role_has_permissions table.
 * Delete when the referred unlimited access role is deleted
 */
class UnlimitedAccessRolePermission extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "permission_id",
        "role_id",
        "unlimited_access_role_id",
    ];
}
