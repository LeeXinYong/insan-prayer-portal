<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrayerTime extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function getData($request):array {
        return [
            "prayer_id" => $this->prayer_id,
            "state_id" => $this->state_id,
            "zone_id" => $this->zone_id,
            "hijri_date" => $this->hijri_date,
            "gregorian_date" => $this->gregorian_date,
            "day" => $this->day,
            "imsak" => $this->imsak,
            "fajr" => $this->fajr,
            "syuruk" => $this->syuruk,
            "dhuhr" => $this->dhuhr,
            "asr" => $this->asr,
            "maghrib" => $this->maghrib,
            "isha" => $this->isha
        ];
    }
}
