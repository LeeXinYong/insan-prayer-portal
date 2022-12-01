<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrayerTime extends Model
{
    // use HasFactory;

    protected $table = 'prayer_times';
    protected $primaryKey = 'prayer_id';
    
    const UPDATED_AT = null;

    public function zone() {
        return $this->belongsTo(Zone::class, 'zone_id', 'zone_id');
    }
}
