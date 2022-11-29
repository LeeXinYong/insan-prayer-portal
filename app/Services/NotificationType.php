<?php

namespace App\Services;

use App\Broadcasting\PushNotificationChannels\FCMChannel;
use App\Broadcasting\PushNotificationChannels\OneSignalChannel;

enum NotificationType
{
    case Database;
    case Mail;
    case OneSignal;
    case FCM;

    public function toChannel(): string
    {
        return match($this) {
            self::Database => 'database',
            self::Mail => 'mail',
            self::OneSignal => config('notifications.channels.'.self::OneSignal->toString().".driver"),
            self::FCM => config('notifications.channels.'.self::FCM->toString().".driver"),
        };
    }

    public function toString(): string
    {
        return match($this) {
            self::Database => 'Database',
            self::Mail => 'Mail',
            self::OneSignal => 'OneSignal',
            self::FCM => 'FCM',
        };
    }
}
