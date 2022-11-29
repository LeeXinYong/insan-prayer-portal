<?php

namespace App\Models\Notifications;

use App\Traits\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationIdentifier extends Model
{
    use HasFactory, UseUuid;

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'identifiable_type',
        'identifiable_id',
    ];
}
