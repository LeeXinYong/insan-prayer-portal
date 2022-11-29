<?php

namespace App\Http\Resources\Helpers\Traits;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Pagination\LengthAwarePaginator;
use function request;

trait WithRelationships
{
    use VerifyInheritance;

    abstract public static function relationships(): array;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \App\Http\Resources\Helpers\Exceptions\WrongInheritanceException
     */
    public static function loadRelationships($resource)
    {
        self::verifyInheritance();

        if ($resource instanceof MissingValue) {
            return $resource;
        }

        foreach (static::relationships() as $relationship => $relationship_name) {
            if (request()->get("with" . ucfirst($relationship))) {
                if ($resource instanceof LengthAwarePaginator) {
                    foreach ($resource->getCollection() as $item) {
                        $item->load($relationship_name);
                    }
                }

                if ($resource instanceof Builder || $resource instanceof \Illuminate\Database\Eloquent\Builder) {
                    $eagerLoadingRelation = array_reduce(explode(".", $relationship_name), function ($carry, $relation) {
                        return $carry->getRelation($relation);
                    }, $resource);
                    $resource->eagerLoadRelations([$eagerLoadingRelation->getRelated()]);
                    $resource->with($relationship_name);
                }

                if (is_iterable($resource)) {
                    foreach ($resource as $item) {
                        self::loadRelation($item, $relationship_name);
                    }
                }
            }
        }
        return $resource;
    }

    private static function loadRelation($items, $relation)
    {
        $relations = explode(".", $relation);

        $relation = array_shift($relations);

        if (is_iterable($items)) {
            foreach ($items as $item) {
                if (!$item->relationLoaded($relation)) {
                    $item->load($relation);
                }
            }
        } else {
            if (!$items->relationLoaded($relation)) {
                $items->load($relation);
            }
        }

        if (sizeof($relations) > 0) {
            self::loadRelation($items->$relation, implode(".", $relations));
        }

        return $items;
    }
}
