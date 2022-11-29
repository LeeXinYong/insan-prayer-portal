<?php

namespace App\Models;

use App\Traits\HasNotificationIdentifiers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class TestRecipient extends Model
{
    use HasFactory;

    public function notifiable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public static function getAllNotifiables(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
    {
        return static::all()->map(fn($t) => $t->notifiable);
    }
}
