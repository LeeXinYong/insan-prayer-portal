<?php

namespace App\Http\Resources\Helpers\Traits;

use App\Http\Resources\Helpers\Exceptions\WrongInheritanceException;
use App\Http\Resources\Helpers\InterceptedJsonResource;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use function request;

trait WithPagination
{
    use VerifyInheritance;

    public static bool $paginate = true;
    public static int $perPage = 0;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws WrongInheritanceException
     */
    public static function paginate($resource)
    {
        self::verifyInheritance();

        $perPage = request()->get("perpage", self::$perPage);

        if ($perPage <= 0) {
            self::$paginate = false;
        }

        if (!self::$paginate) {
            return $resource;
        }

        if ($resource instanceof MissingValue) {
            return $resource;
        }

        // if it is already paginated, return it
        if ($resource instanceof LengthAwarePaginator) {
            return $resource;
        }

        if ($resource instanceof Relation) {
            return $resource->getBaseQuery()->paginate($perPage);
        }

        if ($resource instanceof Builder || $resource instanceof \Illuminate\Database\Eloquent\Builder) {
            return $resource->paginate($perPage);
        }

        if (is_iterable($resource)) {
            if (!isset(collect($resource)->chunk($perPage)[Paginator::resolveCurrentPage() - 1])) {
                return new LengthAwarePaginator([], 0, $perPage, Paginator::resolveCurrentPage(), [
                    'path' => Paginator::resolveCurrentPath(),
                    'query' => Paginator::resolveQueryString(),
                ]);
            }
            return new LengthAwarePaginator(
                collect($resource)->chunk($perPage)[Paginator::resolveCurrentPage() - 1],
                count($resource),
                $perPage,
                Paginator::resolveCurrentPage(),
                [
                    'path' => Paginator::resolveCurrentPath(),
                    'query' => Paginator::resolveQueryString(),
                ]
            );
        }

        throw new \Exception("Resource must be an instance of Illuminate\Database\Eloquent\Builder, Illuminate\Pagination\LengthAwarePaginator or Illuminate\Support\Collection, get : " . get_class($resource));

    }
}
