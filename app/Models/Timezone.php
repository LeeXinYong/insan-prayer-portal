<?php

namespace App\Models;

use App\Core\Traits\SpatieLogsActivity;
use App\Traits\ModelTrait;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    use HasFactory, ModelTrait, SpatieLogsActivity;

    /**
     * The appended attributes.
     *
     * @var array
     */
    protected $appends = ["offset", "timezone_name"];

    public function getOffsetAttribute(): string
    {
        $offset = (new DateTimeZone($this->name))->getOffset(new DateTime);
        return "(GMT " . sprintf("%+03d", $offset / 3600) . ":" . sprintf("%02d", $offset % 3600 / 60) . ")";
    }

    public function getTimezoneNameAttribute(): string
    {
        $timezone_name = (new DateTimeZone($this->name))->getLocation()["comments"];
        if(empty($timezone_name)) {
            $timezone_name = (new DateTimeZone($this->name))->getName();
        }
        return $timezone_name;
    }
}
