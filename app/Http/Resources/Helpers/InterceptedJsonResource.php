<?php

namespace App\Http\Resources\Helpers;

use App\Models\LocalizedModel;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use ReflectionClass;

abstract class InterceptedJsonResource extends JsonResource
{
    /**
     * Create a new anonymous resource collection.
     *
     * @param  mixed  $resource
     * @return AnonymousResourceCollection
     */
    public static function collection($resource): AnonymousResourceCollection
    {
        if (method_exists(static::class, 'loadRelationships')) {
            $resource = static::loadRelationships($resource);
        }

        if (method_exists(static::class, 'filterable')) {
            $resource = static::filterable($resource);
        }

        if (method_exists(static::class, 'searchable')) {
            $resource = static::searchable($resource);
        }

        if (method_exists(static::class, 'paginate') && (new ReflectionClass(static::class))->getStaticPropertyValue('paginate', false)) {
            $resource = static::paginate($resource);
        }

        if ($resource instanceof Builder || $resource instanceof \Illuminate\Database\Eloquent\Builder){
            $resource = $resource->get();
        }

        return parent::collection($resource);
    }

    abstract public function getData($request): array;

    public function toArray($request): array
    {
        $request = $this->getData($request);

        if (method_exists(static::class, 'modifyData')) {
            $request = static::modifyData($request);
        }

        return $request;
    }

    public function __get($key)
    {
        if ($this->resource instanceof LocalizedModel) {
            if (request()->get('lang', auth()->user()?->locale ?? null) === 'vi')
                return $this->resource->{'localized_vi_'.$key} ?? $this->resource->{$key};
        }
        return $this->resource->{$key};
    }

    public function getLocaleField($key): string
    {
        if ($this->resource instanceof LocalizedModel) {
            if (request()->get('lang', auth()->user()?->locale ?? null) === 'vi')
                return 'localized_vi_'.$key ?? $key;
        }
        return $key;
    }
}
