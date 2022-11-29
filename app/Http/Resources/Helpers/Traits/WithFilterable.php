<?php

namespace App\Http\Resources\Helpers\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use function Sodium\compare;

trait WithFilterable
{
    use VerifyInheritance;

    protected static function getFilterJoinKey(): string
    {
        return 'filter_join';
    }

    protected static function getFilterJoin(): string
    {
        $filterJoin = request()->get(self::getFilterJoinKey(), 'and');
        if (in_array($filterJoin, self::$ALLOWED_FILTER_JOINS)) {
            return $filterJoin;
        }
        return 'and';
    }

    protected static function getFilterableKey(): string
    {
        return 'filter';
    }

    protected static function getFilterableFields($resource): array
    {
        return [];
    }

    protected static array $ALLOWED_OPERATORS = ["=", "!=", ">", "<", ">=", "<=", "like"];
    protected static array $ALLOWED_FILTER_JOINS = ["and", "or"];

    /**
     * @throws \App\Http\Resources\Helpers\Exceptions\WrongInheritanceException
     */
    public static function filterable($resource)
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
                $filters = request()->get(static::getFilterableKey());
                foreach ($filters ?? [] as $filter) {
                    $filteringField = $filter['field'];
                    $filteringOperator = $filter['operator'] ?? "=";
                    $filteringValue = $filter['value'];

                    if (!in_array($filteringField, static::getFilterableFields($resource))) {
                        continue;
                    }

                    if (!in_array($filteringOperator, static::$ALLOWED_OPERATORS)) {
                        continue;
                    }

                    if (str_contains($filteringField, ".")) {

                        [$relation, $fieldName] = explode(".", $filteringField);

                        if (method_exists(static::class, "relationships")) {
                            if (array_key_exists($relation, static::relationships())) {
                                $relation = static::relationships()[$relation];
                            }
                        }

                        if (array_key_exists($relation, $resource->getEagerLoads())) {
                            if ($filteringOperator === "like") {
                                $filteringValue = "%{$filteringValue}%";
                            }
                            $query->orWhereRelation($relation, $fieldName, $filteringOperator, $filteringValue);
                            continue;
                        }
                    }

                    if (!Schema::hasColumn($query->from, $filteringField)) {
                        throw new \Exception("Field {$filteringField} does not exists. Please check if you have spelled it correctly, or have loaded necessary relationships.");
                    }

                    $method = match (self::getFilterJoin()) {
                        'and' => 'where',
                        'or' => 'orWhere',
                        default => throw new \Exception("Unknown filter join ".self::getFilterJoin())
                    };

                    $query->{$method}($filteringField, $filteringOperator, $filteringValue);
                }
            });

            return $resource;
        }

        if (is_array($resource) || $resource instanceof Collection) {
            return collect($resource)->filter(function ($item) use ($resource) {
                $filters = request()->get(static::getFilterableKey());

                if (!is_iterable($filters) || count($filters) === 0) {
                    return true;
                }

                $base = match (self::getFilterJoin()) {
                    'and' => true,
                    'or' => false,
                    default => throw new \Exception("Unknown filter join ".self::getFilterJoin())
                };

                $result = $base;

                foreach ($filters as $filter) {
                    $filteringField = $filter['field'];
                    $filteringOperator = $filter['operator'] ?? "=";
                    $filteringValue = $filter['value'];

                    if (!in_array($filteringField, static::getFilterableFields($resource))) {
                        continue;
                    }

                    if (!in_array($filteringOperator, static::$ALLOWED_OPERATORS)) {
                        continue;
                    }

                    if (str_contains($filteringField, ".")) {
                        [$relation, $fieldName] = explode(".", $filteringField);
                        $array = collect($item)->toArray();
                        if (array_key_exists($relation, $array) && array_key_exists($fieldName, $array[$relation])) {
                            if ($filteringOperator === '=') {
                                if (!($array[$relation][$fieldName] == $filteringValue))
                                    $result = !$base;
                                break;
                            }

                            if ($filteringOperator === '!=') {
                                if (!($array[$relation][$fieldName] != $filteringValue))
                                    $result = !$base;
                                break;
                            }

                            if ($filteringOperator === '>') {
                                if (!($array[$relation][$fieldName] > $filteringValue))
                                    $result = !$base;
                                break;
                            }

                            if ($filteringOperator === '<') {
                                if (!($array[$relation][$fieldName] < $filteringValue))
                                    $result = !$base;
                                break;
                            }

                            if ($filteringOperator === '>=') {
                                if (!($array[$relation][$fieldName] >= $filteringValue))
                                    $result = !$base;
                                break;
                            }

                            if ($filteringOperator === '<=') {
                                if (!($array[$relation][$fieldName] <= $filteringValue))
                                    $result = !$base;
                                break;
                            }

                            if ($filteringOperator === 'like') {
                                if (!(str_contains($array[$relation][$fieldName], $filteringValue)))
                                    $result = !$base;
                                break;
                            }
                        }
                    }

                    if ($filteringOperator === '=') {
                        if (!($item[$filteringField] == $filteringValue))
                            $result = !$base;
                        break;
                    }

                    if ($filteringOperator === '!=') {
                        if (!($item[$filteringField] != $filteringValue))
                            $result = !$base;
                        break;
                    }

                    if ($filteringOperator === '>') {
                        if (!($item[$filteringField] > $filteringValue))
                            $result = !$base;
                        break;
                    }

                    if ($filteringOperator === '<') {
                        if (!($item[$filteringField] < $filteringValue))
                            $result = !$base;
                        break;
                    }

                    if ($filteringOperator === '>=') {
                        if (!($item[$filteringField] >= $filteringValue))
                            $result = !$base;
                        break;
                    }

                    if ($filteringOperator === '<=') {
                        if (!($item[$filteringField] <= $filteringValue))
                            $result = !$base;
                        break;
                    }

                    if ($filteringOperator === 'like') {
                        if (!(str_contains($item[$filteringField], $filteringValue)))
                            $result = !$base;
                        break;
                    }

                }

                return $result;
            });
        }

        throw new \Exception("Resource must be an instance of Illuminate\Database\Eloquent\Builder or Illuminate\Support\Collection, get : " . get_class($resource));

    }
}
