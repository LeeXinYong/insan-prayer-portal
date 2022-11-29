<?php

namespace App\Console\Commands;

use App\Providers\NotificationServiceProvider;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;

class NotificationMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare and migrate notifications-related tables required';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = app(PushNotificationService::class);
        return $service->setIgnoreMigrations(false)->migrate($this->output);
    }
}
