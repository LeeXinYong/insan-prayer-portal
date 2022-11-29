<?php

namespace App\Http\Resources\Helpers\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

trait WithSearchable
{
    use VerifyInheritance;

    protected static function getSearchKey() : string
    {
        return 'search';
    }

    protected static function getSearchableFields($resource) : array
    {
        return ["name"];
    }

    public static function searchable($resource)
    {
        self::verifyInheritance();

        if ($resource instanceof MissingValue) {
            return $resource;
        }

        if ($resource instanceof Relation) {
            $resource = $resource->getBaseQuery();
        }

        if ($resource instanceof Builder || $resource instanceof \Illuminate\Database\Eloquent\Builder) {
            $resource->where(function ($query) use ($resource) {
                $searchKey = request()->get(static::getSearchKey());
                $searchKey = addslashes($searchKey);
                $searchableFields = static::getSearchableFields($resource);
                foreach ($searchableFields as $field) {
                    if (str_contains($field, ".")) {
                        if (array_key_exists(strstr($field, ".", true), $resource->getEagerLoads())) {
                            $exploded = explode(".", $field);
                            $fieldName = array_pop($exploded);
                            $relation = implode(".", $exploded);
                            $query->orWhereRelation($relation, $fieldName, "like", "%{$searchKey}%");
                            continue;
                        }
                        if (!Schema::hasColumn($query->from, $field)) {
                            continue;
                        }
                    }
                    $query->orWhere($field, 'like', "%{$searchKey}%");
                }
            });

            return $resource;
        }

        if (is_array($resource) || $resource instanceof Collection) {
            return collect($resource)->filter(function ($item) use ($resource) {
                $searchKey = request()->get(static::getSearchKey());
                $searchableFields = static::getSearchableFields($resource);
                foreach ($searchableFields as $field) {
                    if (str_contains($field, ".")) {
                        [$relation, $fieldName] = explode(".", $field);
                        $array = collect($item)->toArray();
                        if (array_key_exists($relation, $array) && array_key_exists($fieldName, $array[$relation])) {
                            if (str_contains(strtolower($array[$relation][$fieldName]), strtolower($searchKey))) {
                                return true;
                            }
                        }
                    }
                    if (str_contains(strtolower($item[$field]), strtolower($searchKey))) {
                        return true;
                    }
                }
                return false;
            });
        }

        throw new \Exception("Resource must be an instance of Illuminate\Database\Eloquent\Builder or Illuminate\Support\Collection, get : " . get_class($resource));

    }
}
