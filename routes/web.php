<?php

use App\Http\Controllers\Auth\SocialiteLoginController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrochureController;
use App\Http\Controllers\ChangelogController;
use App\Http\Controllers\DeviceManageController;
use App\Http\Controllers\Settings\EmailSettingController;
use App\Http\Controllers\Settings\EmailTemplateController;
use App\Http\Controllers\Settings\GeneralSettingController;
use App\Http\Controllers\Settings\FailedJobWebhooksController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\Documentation\ReferencesController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\Logs\AuditLogsController;
use App\Http\Controllers\Logs\DownloadLogsController;
use App\Http\Controllers\Logs\SystemLogsController;
use App\Http\Controllers\Logs\BackupLogsController;
use App\Http\Controllers\Logs\FailedJobLogsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TextController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PrayerTimeController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\IpWhitelistController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get("/", function () {
//     return redirect("index");
// });

$menu = theme()->getMenu();
array_walk($menu, function ($val) {
    if (isset($val["path"])) {
        $route = Route::get($val["path"], [PagesController::class, "index"]);

        // Exclude documentation from auth middleware
        if (!Str::contains($val["path"], "documentation")) {
            $route->middleware(["auth", "verify_session", "password.update", "home_redirect"]);
        }
    }
});

// Functions
// File Manager
Route::get("getFile/{file_module}/{module_id}/{file_path_field}/{file_name?}", [FileController::class, "getFileViaLink"])->name("getFile");

// Country Manager
Route::get("getCountryTimezone/{country_id}", [CountryController::class, "getCountryTimezone"])->name("getCountryTimezone");

// Text Manager
Route::get("getText", [TextController::class, "getText"])->name("getText");

// Device Manager
Route::get("/trust-device/{token}", [DeviceManageController::class, "trustDevice"])->name("device.trust");
Route::get("/block-device/{token}", [DeviceManageController::class, "blockDevice"])->name("device.block");
Route::post("/block-device/{token}", [DeviceManageController::class, "updateNewPassword"]);

// Documentations pages
Route::prefix("documentation")->group(function () {
    Route::get("getting-started/references", [ReferencesController::class, "index"]);
    Route::get("getting-started/changelog", [PagesController::class, "index"]);
});

Route::middleware(["auth", "verify_session"])->group(function () {
    // ----- FORCE CHANGE PASSWORD -----
    Route::get("/update-password", [ProfileController::class, "firstTimeLogin"])->name("firsttimelogin");
    Route::post("/update-password", [ProfileController::class, "updateInitialPassword"])->name("firsttimelogin.update_password");

    Route::middleware("password.update")->group(function () {
        // Banner pages
        Route::resource("banner", BannerController::class)->except(["show"]);
        Route::prefix("banner")->name("banner.")->group(function () {
            // Route::get("arrange", [BannerController::class, "arrange"])->name("arrange");
            Route::post("arrange", [BannerController::class, "rearrange"])->name("rearrange");
        });

        // Brochure pages
        Route::resource("brochure", BrochureController::class)->except(["show"]);
        Route::prefix("brochure")->name("brochure.")->group(function () {
            // Route::get("arrange", [BrochureController::class, "arrange"])->name("arrange");
            Route::post("arrange", [BrochureController::class, "rearrange"])->name("rearrange");
        });

        // Video pages
        Route::resource("video", VideoController::class)->except(["show"]);
        Route::prefix("video")->name("video.")->group(function () {
            Route::get("fetchData", [VideoController::class, "fetchData"])->name("fetchData");
            // Route::get("arrange", [VideoController::class, "arrange"])->name("arrange");
            Route::post("arrange", [VideoController::class, "rearrange"])->name("rearrange");
        });

        // Guide pages
        Route::resource("guide", GuideController::class)->except(["show"]);
        Route::prefix("guide")->name("guide.")->group(function () {
            // Route::get("arrange", [GuideController::class, "arrange"])->name("arrange");
            Route::post("arrange", [GuideController::class, "rearrange"])->name("rearrange");
        });

        // News pages
        Route::resource("news", NewsController::class)->except(["show"]);
        Route::prefix("news")->name("news.")->group(function () {
            // Route::get("arrange", [NewsController::class, "arrange"])->name("arrange");
            Route::post("arrange", [NewsController::class, "rearrange"])->name("rearrange");
        });

        // Notification pages
        Route::resource("notification", \App\Http\Controllers\PushNotificationController::class)->only(["index", "create", "store"]);
        Route::post("notification/test", [\App\Http\Controllers\PushNotificationController::class, "test"])->name("notification.test");
        Route::get("notification/test-recipients", [\App\Http\Controllers\TestRecipientController::class, "index"])->name("notification.testRecipients.index");
        Route::post("notification/test-recipients/{user}", [\App\Http\Controllers\TestRecipientController::class, "update"])->name("notification.testRecipients.update");

        // User pages
        Route::resource("user", UserController::class);

        Route::prefix("user")->name("user.")->group(function () {
            Route::post("{user}/status/{action}", [UserController::class, "updateStatus"])->name("updateStatus");
            Route::post("{user}/password", [UserController::class, "updatePassword"])->name("updatePassword");
            Route::post("{user}/sendTestNotification", [UserController::class, "sendTestNotification"])->name("sendTestNotification");
            Route::get("{user}/getUserActivities", [UserController::class, "getUserActivities"])->name("getUserActivities");
        });

        // Roles
        Route::resource("role", RoleController::class)->except([
            "edit", "create"
        ]);
        Route::get("role/{role}/users", [RoleController::class, "showUsers"])->name("role.show.users");
        Route::put("role/{role}/users", [RoleController::class, "updateUser"])->name("role.update.users");
        Route::put("role/{role}/permissions", [RoleController::class, "updatePermissions"])->name("role.update.permissions");

        Route::get("permission", [\App\Http\Controllers\PermissionController::class, "index"])->name("permission.index");

        // Profile pages
        Route::prefix("profile")->name("profile.")->group(function () {
            Route::get("view", [ProfileController::class, "view"])->name("view");
            Route::put("update", [ProfileController::class, "update"])->name("update");
            Route::get("change-password", [ProfileController::class, "changePassword"])->name("changePassword");
            Route::post("change-password", [ProfileController::class, "updatePassword"])->name("updatePassword");
        });

        // Systems
        Route::prefix("system")->name("system.")->group(function () {
            // Settings
            Route::prefix("settings")->name("settings.")->group(function () {
                // General Setting
                Route::resource("general", GeneralSettingController::class)->only(["index"]);
                Route::post("general/update", [GeneralSettingController::class, "update"])->name("general.update");

                // Email Server
                Route::resource("emailserver", EmailSettingController::class)
                    ->only([
                        "index", "store", "update",
                    ]);

                // Email Templates
                Route::resource("emailtemplate", EmailTemplateController::class)
                    ->only([
                        "index", "edit", "update",
                    ]);
                Route::prefix("emailtemplate")->name("emailtemplate.")->group(function () {
                    Route::post("{emailtemplate}/testemail", [EmailTemplateController::class, "testemail"])->name("test");
                });

                // Failed Jobs Webhook
                Route::resource("failed_job_webhook", FailedJobWebhooksController::class)->except(["show"]);
                Route::prefix("failed_job_webhook")->name("failed_job_webhook.")->group(function () {
                    Route::post("{failed_job_webhook}/test", [FailedJobWebhooksController::class, "test"])->name("test");
                    Route::post("{failed_job_webhook}/secretkey", [FailedJobWebhooksController::class, "refreshSecretKey"])->name("regenerateSecretKey");
                });
            });

            // Logs pages
            Route::prefix("log")->name("log.")->group(function () {
                Route::resource("system", SystemLogsController::class)->only(["index", "destroy"]);
                Route::resource("audit", AuditLogsController::class)->only(["index", "destroy"]);
                Route::resource("download", DownloadLogsController::class)->only(["index"]);
                Route::resource("backup", BackupLogsController::class)->only(["index", "destroy"]);
                Route::prefix("backup")->name("backup.")->group(function () {
                    Route::get("destinationstatus", [BackupLogsController::class, "getBackupDestinationStatus"])->name("destinationstatus");
                });
                Route::resource("failed_job", FailedJobLogsController::class)->only(["index", "destroy"]);
                Route::prefix("failed_job")->name("failed_job.")->group(function () {
                    Route::post("{id}/retry", [FailedJobLogsController::class, "retry"])->name("retry");
                });
                Route::resource("changelog", ChangelogController::class)->except(["show"]);
                Route::get("system/download/{channel}/{file}", [SystemLogsController::class, "download"])->name("system.download");
            });
        });
        
        // Prayer Time pages
        Route::resource("prayer_time", PrayerTimeController::class);
        
        // Zone pages
        Route::resource("zone", ZoneController::class);
        
        // Credential pages
        Route::resource("credential", CredentialController::class);
        
        // Credential pages
        Route::resource("ip_whitelist", IpWhitelistController::class);
    });
});

Route::get('/betteruptime', function () {
    $db = "OK";
    try {
        DB::connection()->getPdo();
    } catch (\Exception $e) {
        $db = "Disconnected";
    }

    return json_encode([
        "host" => "OK",
        "db" => $db
    ], JSON_PRETTY_PRINT);
});

/**
 * Socialite login using Google service
 * https://laravel.com/docs/8.x/socialite
 */
Route::get("/auth/redirect/{provider}", [SocialiteLoginController::class, "redirect"])->name("socialite.redirect");

require __DIR__."/auth.php";
