<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    // use HasFactory;

    protected $table = 'credentials';
    protected $primaryKey = 'consumer_id';
    protected $keyType = 'string';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
