<?php

use App\Http\Controllers\UserController;
use App\Models\BackupLog;
use App\Models\Banner;
use App\Models\Brochure;
use App\Models\Changelog;
use App\Models\Download;
use App\Models\EmailServer;
use App\Models\EmailTemplate;
use App\Models\Guide;
use App\Models\PushNotification;
use App\Models\News;
use App\Models\SysParam;
use App\Models\TestRecipient;
use App\Models\FailedJobWebhook;
use App\Models\User;
use App\Models\Video;
use App\Models\PrayerTime;
use App\Models\Zone;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return [
    "policies" => [
//        \App\Models\User::class => \App\Policies\UserPolicy::class,
    ],

    /**
     * Default policy methods = [ "viewAny", "view", "create", "update", "delete" ]
     * check in AuthServiceProvider::getPolicyDefaultPermissions()
     *
     * For except, pass an array of methods that want to be excluded from the default policy.
     *
     * For add, pass an array of methods that want to be added to the default policy.
     * Every element in the array should be just the method name as value, or the method name as key and additional checking as the value.
     * The additional checking should be in the format of callable (https://www.php.net/manual/en/language.types.callable.php).
     *
     */
    "anonymous_policies" => [

        "Dashboard" => [
            "except" => ["viewAny", "update", "create", "delete"],
        ],

        Banner::class => [
            "add" => ["arrange"],
            "except" => ["view"],
        ],

        Brochure::class => [
            "add" => ["arrange"],
            "except" => ["view"],
        ],

        News::class => [
            "add" => ["arrange"],
            "except" => ["view"],
        ],

        Video::class => [
            "add" => ["arrange"],
            "except" => ["view"],
        ],

        PushNotification::class => [
            "except" => ["view", "update", "delete"],
        ],

        TestRecipient::class => [
            "except" => ["view", "create", "delete"],
        ],

        User::class => [
            "add" => [
                "updateStatus" => [UserController::class, "canUpdateStatusAndUpdatePassword"],
                "updatePassword" => [UserController::class, "canUpdateStatusAndUpdatePassword"],
                "sendTestNotification",
            ],
        ],

        Role::class => [
            "add" => [
                "viewUsers" => [\App\Http\Controllers\RoleController::class, "canViewUsers"],
                "updateUsers" => [\App\Http\Controllers\RoleController::class, "canUpdateUsers"],
                "viewPermission" => [\App\Http\Controllers\RoleController::class, "canViewPermission"],
                "updatePermission" => [\App\Http\Controllers\RoleController::class, "canUpdatePermission"],
            ]
        ],

        Permission::class => [
            "except" => ["view", "update", "create", "delete"],
        ],

        SysParam::class => [
            "except" => ["view", "create", "delete"],
        ],

        EmailServer::class => [
            "except" => ["view", "delete"],
        ],

        EmailTemplate::class => [
            "except" => ["view", "create", "delete"],
        ],

        Activity::class => [
            "except" => ["view", "create", "update"],
        ],

        BackupLog::class => [
            "except" => ["view", "create", "update"],
        ],

        Changelog::class => [
            "except" => ["view", "delete"],
        ],

        Download::class => [
            "except" => ["view", "create", "update", "delete"],
        ],

        "FailedJobLog" => [
            "except" => ["update", "view", "create"],
            "add" => [
                "retry",
            ],
        ],

        FailedJobWebhook::class => [
            "except" => ["view", "update"],
            "add" => [
                "send",
                "test",
                "refreshSecretKey",
            ],
        ],

        "SystemLog" => [
            "except" => ["update", "view", "create"],
        ],

        Guide::class => [
            "add" => ["arrange"],
            "except" => ["view"],
        ],

        PrayerTime::class => [
            "except" => ["create", "delete", "view"],
        ],

        Zone::class => [
            "except" => ["view"],
        ],
    ]
];
