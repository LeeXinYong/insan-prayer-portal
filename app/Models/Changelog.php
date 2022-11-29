<?php

namespace App\Models;

use App\Services\DateTimeFormatterService;
use App\Traits\ModelTrait;
use App\Traits\UseUuid;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Changelog extends Model
{
    use HasFactory, ModelTrait, UseUuid;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        "id" => null,
        "version" => null,
        "type" => null,
        "released_by" => null,
        "released_at" => null,
        "description" => null,
        "added_by" => null,
        "added_ip" => null,
        "updated_by" => null,
        "updated_ip" => null,
        "created_at" => null,
        "updated_at" => null,
    ];

    public function getReleasedAtAttribute($value): array|string|Translator|Application|null
    {
        return isset($value) ? DateTimeFormatterService::formatDate($value) : null;
    }

    public function getReleasedAtEpochAttribute(): float|array|int|string|Translator|Application|null
    {
        return $this->getRawOriginal("released_at") !== null ? DateTimeFormatterService::formatModalDateTimeEpoch($this->getRawOriginal("released_at")) : null;
    }
}
