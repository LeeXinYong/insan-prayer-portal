<?php

namespace App\Core\Adapters;

use App\Models\BackupLog;
use App\Models\Banner;
use App\Models\Brochure;
use App\Models\Changelog;
use App\Models\Download;
use App\Models\EmailTemplate;
use App\Models\FailedJobWebhook;
use App\Models\Guide;
use App\Models\News;
use App\Models\PushNotification;
use App\Models\SysParam;
use App\Models\TestRecipient;
use App\Models\User;
use App\Models\Video;
use App\Providers\AuthServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Adapter class to make the Metronic core lib compatible with the Laravel functions
 *
 * Class Menu
 *
 * @package App\Core\Adapters
 */
class Menu extends \App\Core\Menu
{
    public function build()
    {
        ob_start();

        parent::build();

        return ob_get_clean();
    }

    /**
     * Filter menu item based on the user permission using Spatie plugin
     *
     * @param $array
     */
    public static function filterMenuPermissions(&$array)
    {
        if (!is_array($array)) {
            return;
        }

        $user = auth()->user();

        $checkPermission = $checkRole = false;
        if (auth()->check()) {
            // check if the spatie plugin functions exist
            $checkPermission = method_exists($user, 'hasAnyPermission');
            $checkRole = method_exists($user, 'hasAnyRole');
        }

        foreach ($array as $key => &$value) {
            if (is_callable($value)) {
                continue;
            }

            if ($checkPermission && !isset($value['permission']) && isset($value['path'])) {
                $route = self::findRoute($value['path']);
                $routeMiddlewares = $route->gatherMiddleware();

                foreach ($routeMiddlewares as $routeMiddleware) {
                    if ($routeMiddleware instanceof \Closure) {
                        $request = request();
                        try {
                            $routeMiddleware($request, function ($req) use ($request) {
                                return $req !== $request;
                            });
                        } catch (\Exception) {
                            unset($array[$key]);
                            break;
                        }
                        continue;
                    }

                    if (!str_starts_with($routeMiddleware, 'can')) continue;

                    [$action, $ability, $model] = explode(":", str_replace(",", ":", $routeMiddleware));

                    $model = self::getModel($model);

                    if ($user->{$action}($ability, $model)) continue;

                    unset($array[$key]);
                    break;
                }
            }

            if ($checkPermission && isset($value['permission']) && !$user->hasAnyPermission((array)$value['permission'])) {
                unset($array[$key]);
            }

            if ($checkRole && isset($value['role']) && !$user->hasAnyRole((array)$value['role'])) {
                unset($array[$key]);
            }

            if (is_array($value)) {
                self::filterMenuPermissions($value);

                if (isset($value['sub']) && isset($value['sub']['items']) && empty($value['sub']['items'])) {
                    unset($array[$key]);
                }
            }
        }
    }

    private static function findRoute($path)
    {
        $routes = app(Router::class)->getRoutes();
        if ($path === "") {
            $path = "/";
        }
        return Route::has($path) ? $routes->getByName($path) : collect($routes->getRoutes())->firstWhere("uri", "=", $path);
    }

    private static function getModel($model)
    {
        if ($model instanceof Model) return $model;

        if (str_contains($model, '\\')) return trim($model);

        return request()->route($model) ?: ((preg_match("/^['\"](.*)['\"]$/", trim($model), $matches)) ? $matches[1] : null);
    }

    public static function shouldHideModulesTag(): bool
    {
        return !(
            self::verifyPermissions(Banner::class) ||
            self::verifyPermissions(Brochure::class) ||
            self::verifyPermissions(News::class) ||
            self::verifyPermissions(PushNotification::class) ||
            self::verifyPermissions(TestRecipient::class) ||
            self::verifyPermissions(Video::class)
        );
    }

    public static function shouldHideAdminTag(): bool
    {
        return !(
            self::verifyPermissions(User::class) ||
            self::verifyPermissions(Role::class) ||
            self::verifyPermissions(Permission::class) ||
            self::verifyPermissions(SysParam::class) ||
            self::verifyPermissions(EmailTemplate::class) ||
            self::verifyPermissions(Activity::class) ||
            self::verifyPermissions(BackupLog::class) ||
            self::verifyPermissions(Changelog::class) ||
            self::verifyPermissions(Download::class) ||
            self::verifyPermissions("FailedJobLog") ||
            self::verifyPermissions(FailedJobWebhook::class) ||
            self::verifyPermissions("SystemLog")
        );
    }

    public static function shouldHideSupportTag(): bool
    {
        return !(
            self::verifyPermissions(Guide::class)
        );
    }

    public static function verifyPermissions($index): bool
    {
        $default_permissions = AuthServiceProvider::getPolicyDefaultPermissions();
        $module_permissions = config("policies.anonymous_policies." . $index);
        $special_permissions = [];

        // Handle those special permissions that cannot check via canany
        if(isset($module_permissions["add"]) && is_array($module_permissions["add"]) && !empty($module_permissions["add"])) {
            array_walk($module_permissions["add"], function($module_permission, $key) use ($module_permissions, &$special_permissions) {
                if(is_array($module_permission) && is_string($key) && !is_numeric($key)) {
                    $special_permissions[] = $key;
                    unset($module_permissions["add"][$key]);
                }
            });
        }

        if(!empty($special_permissions)) {
            return Auth::user()?->canAny(
                    array_diff(array_merge($default_permissions, ($module_permissions["add"] ?? [])), ($module_permissions["except"] ?? [])),
                    $index
                ) ||
                in_array(true, array_map(function($special_permission) use ($index) {
                    return Auth::user()?->hasPermissionTo($index . "::". $special_permission);
                }, $special_permissions));
        } else {
            return Auth::user()?->canAny(
                array_diff(array_merge($default_permissions, ($module_permissions["add"] ?? [])), ($module_permissions["except"] ?? [])),
                $index
            );
        }
    }
}
