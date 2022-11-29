<?php

namespace App\Services;

use App\Console\Commands\NotifiableIdentifierTableCommand;
use Illuminate\Console\OutputStyle as Output;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Notifications\Console\NotificationTableCommand;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Spatie\Backup\Notifications\Notifiable;

class PushNotificationService
{
    const SUGGESTED_MAX_TITLE_COUNT = 50;
    const SUGGESTED_MAX_MESSAGE_COUNT = 178;

    private bool $ignoreMigrations = false;

    private bool $isImageEnabled = false;
    private bool $isLargeIconEnabled = false;

    /**
     * @var NotificationType[] $channels
     */
    private iterable $channels = [];

    public function __construct($channels = [])
    {
        $this->channels = $channels;

        $this->migrate();
    }

    /**
     * @param bool $ignoreMigrations
     */
    public function setIgnoreMigrations(bool $ignoreMigrations): static
    {
        $this->ignoreMigrations = $ignoreMigrations;
        return $this;
    }

    public function migrate(Output $output = null): int
    {
        if ($this->ignoreMigrations) {
            return 1;
        }

        $runMigrations = false;

        if (!$this->notificationIdentifiersTableExists() && $this->isUsingChannel(NotificationType::FCM) || $this->isUsingChannel(NotificationType::OneSignal)) {
            $this->printMessage('Creating notification identifiers table...', $output);
            $this->prepareNotificationIdentifiersTable();
            $this->printMessage('Notification identifiers table created.', $output);
            $runMigrations = true;
        }

        if (!$this->notificationsTableExists() && $this->isUsingChannel(NotificationType::Database)) {
            $this->printMessage('Creating notifications table...', $output);
            $this->prepareNotificationsTable();
            $this->printMessage('Notifications table created.', $output);
            $runMigrations = true;
        }

        if ($runMigrations) {
            $this->printMessage('Running migrations...', $output);
            $this->migrateTables();
            $this->printMessage('Migrations completed.', $output);
        }

        return 0;
    }

    private function printMessage(string $message, Output $output = null): void
    {
        if ($output) {
            $output->writeln($message);
        } else {
            echo $message . PHP_EOL;
        }
    }

    /**
     * Check if is using a certain channel.
     *
     * @param NotificationType $channel
     * @return bool
     */
    public function isUsingChannel(NotificationType $channel): bool
    {
        return in_array($channel, $this->getChannels());
    }

    /**
     * Add a channel to the list of channels.
     *
     * @param NotificationType ...$channels
     * @return $this
     */
    public function setChannels(NotificationType ...$channels): static
    {
        $this->channels = array_merge($this->channels, $channels);
        return $this;
    }

    public function disableChannels(NotificationType $channels): static
    {
        $this->channels = array_diff($this->channels, $channels);
        return $this;
    }

    /**
     * Channels used to send notifications.
     *
     * @return NotificationType[]
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    /**
     * Channels used to send notifications, formatted to be used by Notifications directly.
     * if the channel has shouldNotSend() == true, it will be removed from the list.
     *
     * @return array
     */
    public function via(): array
    {
        return array_filter(array_map(fn($channel) => $channel->toChannel(), $this->getChannels()), function ($channel) {
            if (method_exists($channel, 'shouldNotSend')) {
                return !app($channel)->shouldNotSend();
            }
            return true;
        });
    }

    /** Check if notification table exists */
    private function notificationsTableExists(): bool
    {
        return $this->tableExists('notifications');
    }

    /** Check if notification table exists */
    private function notificationIdentifiersTableExists(): bool
    {
        return $this->tableExists('notification_identifiers');
    }

    private function tableExists($tableName): bool
    {
        return Schema::hasTable($tableName);
    }

    /**
     * Migrate notifications table.
     *
     * @return void
     */
    private function prepareNotificationsTable()
    {
        \Illuminate\Support\Facades\Artisan::call(
            app(NotificationTableCommand::class)->getName(),
        );
    }

    /**
     * Migrate notifications table.
     *
     * @return void
     */
    private function prepareNotificationIdentifiersTable()
    {
        \Illuminate\Support\Facades\Artisan::call(
            app(NotifiableIdentifierTableCommand::class)->getName(),
        );
    }

    private function migrateTables()
    {
        \Illuminate\Support\Facades\Artisan::call(
            app(MigrateCommand::class)->getName(),
            ["--step" => true],
        );
    }


    public function enableImage(bool $isImageEnabled) : static
    {
        $this->isImageEnabled = $isImageEnabled;
        return $this;
    }

    public function isImageEnabled() : bool
    {
        return $this->isImageEnabled;
    }

    public function enableLargeIcon(bool $isLargeIconEnabled) : static
    {
        $this->isLargeIconEnabled = $isLargeIconEnabled;
        return $this;
    }

    public function isLargeIconEnabled() : bool
    {
        return $this->isLargeIconEnabled;
    }

    public function send(Notification $notification, iterable|Notifiable $notifiables, ?iterable $channels = null) : void
    {
        if (!is_iterable($notifiables)) {
            $notifiables = Arr::wrap($notifiables);
        }

        if (!is_null($channels) && method_exists($notification, 'setChannels')) {
            $notification = $notification->setChannels($channels);
        }

        foreach ($notifiables as $notifiable) {
            $notifiable->notify($notification);
        }
    }

}
