<?php

namespace App\Traits;

use App\Models\Notifications\NotificationIdentifier;
use App\Services\NotificationType;

trait HasNotificationIdentifiers
{
    public function notificationIdentifiers(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(NotificationIdentifier::class, 'notifiable');
    }

    public function addNotificationIdentifier(NotificationType $identifiableType, string $identifier)
    {
        $this->notificationIdentifiers()->create([
            'identifiable_type' => $identifiableType->toChannel(),
            'identifiable_id' => $identifier,
        ]);
    }

    public function getNotificationIdentifiers(NotificationType $channel): array
    {
        return $this->notificationIdentifiers()->where('identifiable_type', $channel->toChannel())->pluck("identifiable_id")->toArray();
    }

}
