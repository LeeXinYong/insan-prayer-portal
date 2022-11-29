<?php

namespace App\Providers;

use App\Services\NotificationType;
use App\Services\PushNotificationService;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{


    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $pushNotificationService = new PushNotificationService();

        $pushNotificationService->setChannels(...config('notifications.uses'));

        $pushNotificationService->enableLargeIcon(config('notifications.enables.large_icon', false));
        $pushNotificationService->enableImage(config('notifications.enables.image', false));

        $pushNotificationService->setIgnoreMigrations(true);

        $this->app->instance(PushNotificationService::class, $pushNotificationService);
    }
}
