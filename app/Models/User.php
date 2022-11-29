<?php

namespace App\Models;

use App\Enums\DefaultRole;
use App\Http\Controllers\LoggerController;
use App\Models\UserRoleModifiers\CanHaveOnlyOneRole;
use App\Models\UserRoleModifiers\MustHasAtLeastOneRole;
use App\Services\DateTimeFormatterService;
use App\Traits\CanBeTestRecipient;
use App\Traits\HasNotificationIdentifiers;
use App\Traits\ModelTrait;
use App\Traits\UseUuid;
use Grosv\LaravelPasswordlessLogin\Traits\PasswordlessLogin;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method bool canCreate($parameters)
 * @method bool canDelete($parameters)
 * @method bool canRefreshSecretKey($parameters)
 * @method bool canRetry($parameters)
 * @method bool canTest($parameters)
 * @method bool canUpdate($parameters)
 * @method bool canView($parameters)
 * @method bool canViewAny($parameters)
 * @method bool canViewUsers($parameters)
 * @method bool canUpdateUsers(...$parameters)
 * @method bool canViewPermission($parameters)
 * @method bool canUpdatePermission($parameters)
 * @method bool cannotCreate($parameters)
 * @method bool cannotDelete($parameters)
 * @method bool cannotRefreshSecretKey($parameters)
 * @method bool cannotRetry($parameters)
 * @method bool cannotTest($parameters)
 * @method bool cannotUpdate($parameters)
 * @method bool cannotView($parameters)
 * @method bool cannotViewAny($parameters)
 * @method bool cannotViewUsers($parameters)
 * @method bool cannotUpdateUsers(...$parameters)
 * @method bool cannotViewPermission($parameters)
 * @method bool cannotUpdatePermission($parameters)
 */

class User extends Authenticatable implements MustVerifyEmail, CanHaveOnlyOneRole, MustHasAtLeastOneRole
{
    use HasFactory, ModelTrait, Notifiable, UseUuid, PasswordlessLogin;
    use HasNotificationIdentifiers, CanBeTestRecipient;
    use HasRoles {roles as rawRoles;}

    public function roles(): BelongsToMany
    {
        return $this->rawRoles()
            ->withPivot('model_type')
            ->using(ModelHasRole::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "name",
        "email",
        "api_token",
        "password",
        "last_login",
        "login_ip",
        "force_pwd",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        "password",
        "remember_token",
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, "country_code", "iso2");
    }

    public function timezoneInfo(): BelongsTo
    {
        return $this->belongsTo(Timezone::class, "timezone", "name");
    }

    public function getLastLoginAttribute($value): array|string|Translator|Application|null
    {
        return DateTimeFormatterService::formatModalDateTime($value);
    }

    public function getLastLoginEpochAttribute(): float|array|int|string|Translator|Application|null
    {
        return DateTimeFormatterService::formatModalDateTimeEpoch($this->getRawOriginal("last_login"));
    }

    public function getRecentActivities($limit = 20): Collection
    {
        // Get all logs
        return LoggerController::transformLogs(
            Activity::query()
            ->where("causer_id", $this->id)
            ->orderByDesc("created_at")
            ->take($limit)
            ->get()
        )->groupBy("created_at_date");
    }

    public function onPasswordlessLoginSuccess($request)
    {
        // check if custom url set, redirect to the url
        return ($request->has('redirect_to')) ? redirect($request->redirect_to) : redirect($this->getRedirectUrlAttribute());
    }

    public function __call($method, $parameters)
    {
        if(str_starts_with($method, "cannot")){
            $permission = lcfirst(str_replace("cannot", "", $method));
            return $this->cannot($permission, $parameters);
        }
        else if(str_starts_with($method, "can")){
            $permission = lcfirst(str_replace("can", "", $method));
            return $this->can($permission, $parameters);
        }

        return parent::__call($method, $parameters);
    }

    function defaultRole(): Role
    {
        /** @var Role */
        return Role::query()->where("default_role", "=", DefaultRole::USER)->firstOr(callback: function () {
            throw new RoleDoesNotExist();
        });
    }
}
