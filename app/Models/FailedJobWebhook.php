<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\DateTimeFormatterService;
use App\Traits\ModelTrait;
use App\Traits\UseUuid;

class FailedJobWebhook extends Model
{
    use HasFactory, ModelTrait, UseUuid;

    protected $fillable = ['secret_key', 'endpoint'];

    public function getLastCalledEpochAttribute(): float|array|int|string|Translator|Application|null
    {
        return DateTimeFormatterService::formatModalDateTimeEpoch($this->getRawOriginal("last_called"));
    }

    public function getLastCalledAttribute($value): array|string|Translator|Application|null
    {
        return DateTimeFormatterService::formatModalDateTime($value);
    }
}
