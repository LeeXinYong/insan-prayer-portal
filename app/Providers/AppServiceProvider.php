<?php

namespace App\Providers;

use App\Core\Adapters\Theme;
use App\Exceptions\RoleModifiers\MustHaveAtLeastOneRoleException;
use App\Http\Controllers\Settings\FailedJobWebhooksController;
use App\Http\Middleware\LogApiRequestsAndResponses;
use App\Mixins\CarbonMixin;
use App\Models\ModelHasRole;
use App\Models\UserRoleModifiers\CanHaveOnlyOneRole;
use App\Models\UserRoleModifiers\MustHasAtLeastOneRole;
use App\Models\UnlimitedAccessRole;
use App\Models\UnlimitedAccessRolePermission;
use App\Overwritten\Gate;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use ReflectionException;
use Spatie\Permission\Models\Permission;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     * @throws ReflectionException
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        Carbon::mixin(new CarbonMixin());
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws Throwable
     */
    public function boot()
    {
        // Force set the request protocol to https
        if($this->app->environment() == "production") {
            URL::forceScheme("https");
        }

        LogApiRequestsAndResponses::$logger = Log::channel("tracking.api");

        $this->app->singleton(GateContract::class, function ($app) {
            return new Gate($app, function () use ($app) {
                return call_user_func($app['auth']->userResolver());
            });
        });

        $theme = theme();

        // Share theme adapter class
        View::share("theme", $theme);

        // Set demo globally
        $theme->setDemo(request()->input("demo", config("layout.demo")));

        $theme->initConfig();

        bootstrap()->run();

        if (isRTL()) {
            // RTL html attributes
            Theme::addHtmlAttribute("html", "dir", "rtl");
            Theme::addHtmlAttribute("html", "direction", "rtl");
            Theme::addHtmlAttribute("html", "style", "direction:rtl;");
            Theme::addHtmlAttribute("body", "direction", "rtl");
        }

        Blade::directive("svg", function ($expression) {
            return theme()->getSvgIcon(...explode(",", $expression));
        });

        // failed jobs alert
        Queue::failing(function (JobFailed $event) {
            // $event->connectionName
            // $event->job
            // $event->exception
            FailedJobWebhooksController::failedJobAlert($event);
        });
    }
}
