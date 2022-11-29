<?php

namespace App\Listeners;

use App\Events\FailedJobWebhookEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\Settings\FailedJobWebhooksController;

class DispatchFailedJobWebhookCall
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
     * @param  \App\Events\FailedJobWebhookEvent  $event
     * @return void
     */
    public function handle(FailedJobWebhookEvent $event)
    {
        FailedJobWebhooksController::send($event->event, $event->data, $event->webhook_id);
    }
}
