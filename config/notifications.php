<?php

return [
    "uses" => [
        \App\Services\NotificationType::Database,
        \App\Services\NotificationType::OneSignal,
    ],

    "enables" => [
        "large_icon" => true,
        "image" => true,
    ],

    "channels" => [
        \App\Services\NotificationType::OneSignal->toString() => [
            "driver" => \App\Broadcasting\PushNotificationChannels\OneSignalChannel::class,

            "app_id" => env("ONESIGNAL_APP_ID"),
            "app_secret" => env("ONESIGNAL_APP_SECRET"),
            "api_endpoint" => env("ONESIGNAL_API_ENDPOINT", "https://onesignal.com/api/v1/notifications"),
        ],

        \App\Services\NotificationType::FCM->toString() => [
            "driver" => \App\Broadcasting\PushNotificationChannels\FcmChannel::class,

            "credential_path" => config("services.firebase.credential_path"),
        ],
    ],
];
