<?php

namespace App\Models;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    // use HasFactory;

    protected $table = 'states';
    protected $primaryKey = 'state_id';
    
    const UPDATED_AT = null;

    public function zones() {
        return $this->hasMany(Zone::class, 'state_id', 'state_id');
    }
}
