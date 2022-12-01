<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PrayerTime;
use App\Models\State;

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

    public function state() {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }
}
