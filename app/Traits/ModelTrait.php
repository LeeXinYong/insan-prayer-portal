<?php

namespace App\Traits;

use App\Models\User;
use App\Services\DateTimeFormatterService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait ModelTrait
{
    public function addedby(): BelongsTo
    {
        return $this->belongsTo(User::class, "added_by");
    }

    public function updatedby(): BelongsTo
    {
        return $this->belongsTo(User::class, "updated_by");
    }

    public function getCreatedAtAttribute($value): array|string|Translator|Application|null
    {
        return DateTimeFormatterService::formatModalDateTime($value);
    }

    public function getUpdatedAtAttribute($value): array|string|Translator|Application|null
    {
        return DateTimeFormatterService::formatModalDateTime($value);
    }

    public function getCreatedAtEpochAttribute(): float|array|int|string|Translator|Application|null
    {
        return DateTimeFormatterService::formatModalDateTimeEpoch($this->getRawOriginal("created_at"));
    }

    public function getUpdatedAtEpochAttribute(): float|array|int|string|Translator|Application|null
    {
        return DateTimeFormatterService::formatModalDateTimeEpoch($this->getRawOriginal("updated_at"));
    }

    public function getHumanReadableSize($size): string
    {
        return ($size != null || $size != "") ? number_format($size / 1048576, 2) . " MB" : __("general.message.not_applicable");
    }
}
