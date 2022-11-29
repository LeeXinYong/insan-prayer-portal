<?php

namespace App\Traits\Scopes;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder active()
 */
trait ActiveScope
{
    public function scopeActive($query)
    {
        return $query->where("status",  1);
    }
}
