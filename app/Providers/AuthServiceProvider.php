<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\App;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AuthServiceProvider extends ServiceProvider
{
    public function __construct($app)
    {
        parent::__construct($app);
        $this->policies = config('policies.policies', []);
    }

    /**
     * Get the policies defined on the provider.
     *
     * @return array<class-string, class-string>
     */
    public function policies(): array
    {
        return $this->getAnonymousPolicies() + $this->policies;
    }

    protected function getAnonymousPolicies(): array
    {
        $anonymousPolicies = config('policies.anonymous_policies', []);

        return collect($anonymousPolicies)
            ->mapWithKeys(function ($options, $anonymousPolicy) {
                return [$anonymousPolicy => self::getPolicy($anonymousPolicy, additionalPermissions: $options["add"] ?? [], exceptPermissions: $options["except"] ?? [])];
            })
            ->toArray();
    }

    public static function permissionFormatter($class, $permission)
    {
        return "$class::$permission";
    }

    public static function getPoliciesMethods(): array
    {
        $provider = new static(app());
        $anonymousPolicies = collect($provider->getAnonymousPolicies())
            ->map(function ($policy) {
                return $policy->getPermissions();
            });
        return collect($provider->policies)
            ->map(function ($policy) {
                return get_class_methods($policy);
            })
            ->merge($anonymousPolicies)
            ->toArray();
    }

    public static function getAllPoliciesMethods(): \Illuminate\Support\Traits\EnumeratesValues|\Illuminate\Support\Collection
    {
        $policiesMethods = static::getPoliciesMethods();
        return collect($policiesMethods)
            ->flatMap(function ($permissions, $class) {
                return collect($permissions)->map(function ($permissionFn, $permission) use ($class) {
                    return static::permissionFormatter($class, $permission);
                })->values()->toArray();
            });
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }

    /**
     * @param string $class The class to get the policy for
     * @param array $additionalPermissions Additional permissions to add to the policy
     * @param array $exceptPermissions Permissions to exclude from the policy
     *
     * @return object                       New anonymous policy class for given class
     */
    private static function getPolicy(string $class, array $additionalPermissions = [], array $exceptPermissions = [])
    {
        return new class($class, $additionalPermissions, $exceptPermissions, self::getPolicyDefaultPermissions()) {

            use HandlesAuthorization;

            public function before()
            {
                if (!App::environment("production")) {
//                    return true;
                }
            }

            private string $class;
            private array $additionalPermissions;
            private array $exceptPermissions;
            private array $permissions;

            public function __construct($class, $additionalPermissions, $exceptPermissions, $permissions)
            {
                $this->class = $class;
                $this->additionalPermissions = $additionalPermissions;
                $this->exceptPermissions = $exceptPermissions;
                $this->permissions = $permissions;
            }

            public function getPermissions(): array
            {
                $keyedAdditionalPermissions = collect($this->additionalPermissions)
                    ->mapWithKeys(function ($permission, $key) {
                        return is_string($key) ? [$key => $permission] : [$permission => null];
                    })
                    ->toArray();

                return array_merge(array_flip(array_diff($this->permissions, $this->exceptPermissions)), $keyedAdditionalPermissions);
            }

            public function __call($name, $arguments)
            {
                $permissions = $this->getPermissions();

                $permissionNames = array_keys($permissions);

                if (in_array($name, $permissionNames)) {
                    if (!($arguments[0] instanceof User)) {
                        throw new \InvalidArgumentException("First argument must be an instance of User");
                    }

                    $user = $arguments[0];

                    return $user->checkPermissionTo(AuthServiceProvider::permissionFormatter($this->class, $name)) && (!is_array($permissions[$name]) || call_user_func_array($permissions[$name], $arguments));
                }
            }
        };
    }

    public static function getPolicyDefaultPermissions() : array
    {
        return [
            "viewAny",
            "view",
            "create",
            "update",
            "delete",
        ];
    }
}
