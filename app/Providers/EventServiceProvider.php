<?php

namespace App\Providers;

use App\Listeners\Commands\ConfigCacheListener;
use App\Listeners\LogNotificationSent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Listeners\BackupEventSubscriber;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use App\Events\FailedJobWebhookEvent;
use App\Listeners\DispatchFailedJobWebhookCall;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CommandFinished::class => [
            ConfigCacheListener::class,
        ],
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\UserLastLogin',
        ],

        NotificationSent::class => [
            LogNotificationSent::class,
        ],

        FailedJobWebhookEvent::class => [
            DispatchFailedJobWebhookCall::class
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        BackupEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
