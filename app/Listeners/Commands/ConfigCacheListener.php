<?php

namespace App\Listeners\Commands;

use Database\Seeders\PermissionsSeeder;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class ConfigCacheListener
{

    /**
     * Handle the event.
     *
     * @param CommandFinished $event
     * @return void
     */
    public function handle(CommandFinished $event)
    {
        if (!Schema::hasTable(app(Permission::class)->getTable()))  return;
        if (strtolower($event->command) === 'config:cache') {
            $this->seedPermissions($event->output);
        }
    }

    private function seedPermissions($output)
    {
        $output->writeln("<info>Seeding permissions...</info>");
        app(PermissionsSeeder::class)->run();
        $output->writeln("<info>Permissions seeded.</info>");
    }
}
