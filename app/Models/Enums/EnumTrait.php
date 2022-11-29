<?php

namespace App\Models\Enums;

use App\Exceptions\EnumCaseNotFoundException;

trait EnumTrait
{

    public static function casesWithKey(?callable $keyModifier = null): array
    {
        return collect(static::cases())->mapWithKeys(function ($status) use ($keyModifier) {
            $key = is_callable($keyModifier) ? $keyModifier($status->name) : $status->name;
            return [$key => $status];
        })->toArray();
    }

    public static function guess($value, $suppressError = true): static
    {
        return static::casesWithKey()[$value] ??
            static::casesWithKey('strtolower')[strtolower($value)] ??
            static::casesWithKey([self::class, 'alphanumericOnly'])[self::alphanumericOnly($value)] ??
            static::casesWithKey(fn($_) => strtolower(self::alphanumericOnly($_)))[strtolower(self::alphanumericOnly($value))] ??
            ($suppressError ? static::getDefault() : throw new EnumCaseNotFoundException($value));
    }

    protected static function alphanumericOnly(?string $string)
    {
        return preg_replace('/[^A-Za-z0-9]/m', '', $string);
    }

    /**
     * @throws EnumCaseNotFoundException
     */
    abstract public static function getDefault(): self;
}
