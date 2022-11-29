<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Spatie\Permission\Models\Role;

class ModelHasRole extends MorphPivot
{
    protected $table = 'model_has_roles';

    public $timestamps = false;

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function model()
    {
        return $this->morphTo();
    }
}
