<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PrayerTime;

class Zone extends Model
{
    // use HasFactory;

    protected $table = 'zones';
    protected $primaryKey = 'zone_id';
    protected $keyType = 'string';
    
    const UPDATED_AT = null;

    public function prayer_times() {
        return $this->hasMany(PrayerTime::class, 'zone_id', 'zone_id');
    }
}
