<?php

namespace App\Models;

use App\Core\Traits\SpatieLogsActivity;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory, ModelTrait, SpatieLogsActivity;

    /**
     * The appended attributes.
     *
     * @var array
     */
    protected $appends = ["flag_icon_svg"];

    public function timezone():hasMany
    {
        return $this->hasMany(Timezone::class);
    }

    public function getFlagIconSvgAttribute(): bool|string|null
    {
        $country_name = match ($this->name) {
            "Bahamas The" => "Bahamas",
            "Bonaire, Sint Eustatius and Saba" => "Bonaire",
            "Cocos (Keeling) Islands" => "Cocos Islands",
            "Congo" => "Republic of the Congo",
            "Cote D'Ivoire (Ivory Coast)" => "Ivory Coast",
            "Curaçao" => "curacao",
            "Democratic Republic of the Congo" => "Democratic Republic of Congo",
            "Fiji Islands" => "Fiji",
            "Gambia The" => "Gambia",
            "Guernsey and Alderney" => "Guernsey",
            "Hong Kong S.A.R." => "Hong Kong",
            "Macau S.A.R." => "Macao",
            "Macedonia" => "Republic of Macedonia",
            "Man (Isle of)" => "Isle of Man",
            "Marshall Islands" => "Marshall Island",
            "Palestinian Territory Occupied" => "Palestine",
            "Pitcairn Island" => "Pitcairn Islands",
            "Saint Lucia" => "St Lucia",
            "Saint Vincent And The Grenadines" => "St Vincent And The Grenadines",
            "Saint-Barthelemy" => "St Barts",
            "Sao Tome and Principe" => "Sao Tome and Prince",
            "Sint Maarten (Dutch part)" => "Sint Maarten",
            "Turks And Caicos Islands" => "Turks and Caicos",
            "Vatican City State (Holy See)" => "Vatican City",
            "Virgin Islands (British)" => "British Virgin Islands",
            "Virgin Islands (US)" => "Virgin Islands",
            "Western Sahara" => "Sahrawi Arab Democratic Republic",
            default => $this->name
        };
        if(file_exists(theme()->getImageUrl("flags", str_replace(" ", "-", strtolower($country_name)).".svg"))){
            return "flags/" . str_replace(" ", "-", strtolower($country_name)).".svg";
        } else {
            return null;
        }
    }
}
