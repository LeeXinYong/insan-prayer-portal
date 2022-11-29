<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Guide extends Model
{
    use HasFactory, ModelTrait;

    /**
     * The appended attributes.
     *
     * @var array
     */
    protected $appends = ["total_downloads"];

    /**
     * Get all downloads.
     */
    public function downloads(): MorphMany
    {
        return $this->morphMany(Download::class, "downloadable");
    }

    public function getTotalDownloadsAttribute(): int
    {
        return $this->downloads()->count();
    }

    public function getFileSizeAttribute($value): string
    {
        return $this->getHumanReadableSize($value);
    }
}
