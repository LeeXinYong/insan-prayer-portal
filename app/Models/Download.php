<?php

namespace App\Models;

use App\Services\DateTimeFormatterService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Download extends Model
{
    use HasFactory;

    /**
     * Get the owning downloadable model.
     */
    public function downloadable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getCreatedAtAttribute($value): array|string|Translator|Application|null
    {
        return DateTimeFormatterService::formatModalDateTime($value);
    }

    public function getCreatedAtEpochAttribute(): float|array|int|string|Translator|Application|null
    {
        return DateTimeFormatterService::formatModalDateTimeEpoch($this->getRawOriginal("created_at"));
    }
}
