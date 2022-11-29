<?php

namespace App\Listeners;

use Spatie\Backup\Events\BackupWasSuccessful;
use Spatie\Backup\Events\BackupHasFailed;
use Spatie\Backup\Events\CleanupWasSuccessful;
use Spatie\Backup\Events\CleanupHasFailed;
use Log;
use App\Http\Controllers\Logs\BackupLogsController;

class BackupEventSubscriber
{
    /**
     * Handle BackupWasSuccessful events.
     */
    public function handleBackupWasSuccessful($event)
    {
        // if success, record heartbeat to Better Uptime
        // specify the Better Uptime heartbeat url in config/app.php with .env
        if (\Config::get('app.heartbeat') !== null) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, \Config::get('app.heartbeat'));
            curl_exec($ch);
            curl_close($ch);
        }

        $diskName = "";
        $backupName = \Config::get('app.name');
        $log_message = "Backup was successful.";

        if($event->backupDestination != null) {
            $diskName = $event->backupDestination->diskName();
            $backupName = $event->backupDestination->backupName();
            $log_message = $log_message . " File: " . $event->backupDestination->newestBackup()->path();
        } else {
            $diskName = BackupLogsController::guessDiskName($log_message);
        }

        // Record
        BackupLogsController::log($diskName, $backupName, "Backup", $log_message, 1);
    }

    /**
     * Handle BackupHasFailed events.
     */
    public function handleBackupHasFailed($event)
    {
        $diskName = "";
        $backupName = \Config::get('app.name');
        $log_message = "Backup has failed.";

        if ($event->exception != null) {
            // Some exceptions don't have a message
            $exception_message = (!empty($event->exception->getMessage()) ? trim($event->exception->getMessage()) : 'BackupHasFailed Event Error Exception');

            // Log message
            $log_message = " \"" . $exception_message . " in file '" . $event->exception->getFile() . "' on line '" . $event->exception->getLine() . "'" . "\"";
        }

        if ($event->backupDestination != null) {
            $diskName = $event->backupDestination->diskName();
            $backupName = $event->backupDestination->backupName();
        } else {
            $diskName = BackupLogsController::guessDiskName($log_message);
        }

        // Record
        BackupLogsController::log($diskName, $backupName, "Backup", $log_message, 0);
    }

    /**
     * Handle CleanupWasSuccessful events.
     */
    public function handleCleanupWasSuccessful($event)
    {
        $diskName = "";
        $backupName = \Config::get('app.name');
        $log_message = "Cleanup was successful.";

        if ($event->backupDestination != null) {
            $diskName = $event->backupDestination->diskName();
            $backupName = $event->backupDestination->backupName();
            $usedStorage = $event->backupDestination->usedStorage();
            $log_message = $log_message . " Used storage after cleanup: " . formatSizeUnits($usedStorage);
        } else {
            $diskName = BackupLogsController::guessDiskName($log_message);
        }

        // Record
        BackupLogsController::log($diskName, $backupName, "Clean Up", $log_message, 1);
    }

    /**
     * Handle CleanupHasFailed events.
     */
    public function handleCleanupHasFailed($event)
    {
        $diskName = "";
        $backupName = \Config::get('app.name');
        $log_message = "Cleanup has failed.";

        if ($event->exception != null) {
            // Some exceptions don't have a message
            $exception_message = (!empty($event->exception->getMessage()) ? trim($event->exception->getMessage()) : 'CleanupHasFailed Event Error Exception');

            // Log message
            $log_message = " \"" . $exception_message . " in file '" . $event->exception->getFile() . "' on line '" . $event->exception->getLine() . "'" . "\"";
        }

        if ($event->backupDestination != null) {
            $diskName = $event->backupDestination->diskName();
            $backupName = $event->backupDestination->backupName();
        } else {
            $diskName = BackupLogsController::guessDiskName($log_message);
        }

        // Record
        BackupLogsController::log($diskName, $backupName, "Clean Up", $log_message, 0);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        return [
            BackupWasSuccessful::class => 'handleBackupWasSuccessful',
            BackupHasFailed::class => 'handleBackupHasFailed',
            CleanupWasSuccessful::class => 'handleCleanupWasSuccessful',
            CleanupHasFailed::class => 'handleCleanupHasFailed',
        ];
    }
}

?>
