<?php

namespace App\Http\Middleware;

use App\Models\BackupLog;
use App\Models\Banner;
use App\Models\Brochure;
use App\Models\Changelog;
use App\Models\Download;
use App\Models\EmailServer;
use App\Models\EmailTemplate;
use App\Models\FailedJobWebhook;
use App\Models\Guide;
use App\Models\News;
use App\Models\PushNotification;
use App\Models\SysParam;
use App\Models\TestRecipient;
use App\Models\User;
use App\Models\Video;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HomeRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        // If user does not have permission to dashboard, redirect to first viewAny page
        if(!(Auth::user()?->hasPermissionTo("Dashboard::view") ?? true)) {
            $firstViewAny = "";

            foreach (Auth::user()->getAllPermissions()->sortBy("name") as $permission) {
                $permission_name = explode("::", $permission->name);
                if(($permission_name[1] ?? "") == "viewAny") {
                    $firstViewAny = $permission_name[0];
                    break;
                }
            }
            switch(explode("::", $firstViewAny)[0]) {
                // Modules
                case Banner::class: return redirect()->route("banner.index");
                case Brochure::class: return redirect()->route("brochure.index");
                case News::class: return redirect()->route("news.index");
                case PushNotification::class: return redirect()->route("notification.index");
                case TestRecipient::class: return redirect()->route("notification.testRecipients.index");
                case Video::class: return redirect()->route("video.index");

                // Admin
                case User::class: return redirect()->route("user.index");
                case Role::class: return redirect()->route("role.index");
                case Permission::class: return redirect()->route("permission.index");
                case SysParam::class: return redirect()->route("system.settings.general.index");
                case EmailServer::class: return redirect()->route("system.settings.emailserver.index");
                case EmailTemplate::class: return redirect()->route("system.settings.emailtemplate.index");
                case FailedJobWebhook::class: return redirect()->route("system.settings.failed_job_webhook.index");
                case Activity::class: return redirect()->route("system.log.audit.index");
                case BackupLog::class: return redirect()->route("system.log.backup.index");
                case Changelog::class: return redirect()->route("system.log.changelog.index");
                case Download::class: return redirect()->route("system.log.download.index");
                case "FailedJobLog": return redirect()->route("system.log.failed_job.index");
                case "SystemLog": return redirect()->route("system.log.system.index");

                // Support
                case Guide::class: return redirect()->route("guide.index");
            }
        }
        return $next($request);
    }
}
