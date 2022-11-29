<?php

namespace App\Listeners;

use App\Notifications\PushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogNotificationSent implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param NotificationSent $event
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        if ($event->notification instanceof PushNotification) {
            Log::channel("tracking.notifications")->info(
                "Notification sent to {$event->notifiable->name}",
                [
                    "notification" => $event->notification->toArray($event->notifiable),
                    "notifiable" => $event->notifiable->id,
                ]
            );
        }
    }
}
