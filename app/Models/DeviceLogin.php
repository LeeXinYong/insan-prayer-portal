<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeviceLogin extends Model
{
    use HasFactory, ModelTrait, UseUuid;

    // Device status
    const DEVICE_UNTRUSTED = 0;
    const DEVICE_TRUSTED = 1;
    const DEVICE_BLOCKED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "status"
    ];
}
