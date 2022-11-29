<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

/**
 * This class basically relies on mysql "on delete restrict" to prevent roles from being deleted.
 */
class ProtectedRole extends Model
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
}
