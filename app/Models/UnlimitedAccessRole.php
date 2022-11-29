<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class UnlimitedAccessRole extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "role_id"
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions()
    {
        return $this->hasMany(UnlimitedAccessRolePermission::class);
    }
}
