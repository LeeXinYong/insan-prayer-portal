<?php

namespace App\Http\Resources\Helpers\Traits;

trait WithModifiable
{
    use VerifyInheritance;

    protected static array $excluded = [];

    protected static array $only = [];

    protected static array $renamed = [];

    protected static array $modify = [];

    // fields that should be excluded from the response
    public static function exclude(string ...$item): void
    {
        array_push(static::$excluded, ...$item);
    }
    // only these fields that should be returned from the response
    public static function only(string ...$item): void
    {
        static::$only = $item;
    }

    // fields that should be changed their name
    // $key => $value where $key is the original name and $value is the new name
    public static function rename(array $items): void
    {
        static::$renamed = array_merge(static::$renamed, $items);
    }

    // fields that should be modified from the response
    // string $key => callable $value where $key is the name of the field and $value is the function that returns the value
    public static function modify(array $items): void
    {
        static::$modify = array_merge(static::$modify, $items);
    }

    /**
     * @throws \App\Http\Resources\Helpers\Exceptions\WrongInheritanceException
     */
    public function modifyData($data)
    {
        self::verifyInheritance();

        // remove excluded fields
        foreach(static::$excluded as $excludedField) {
            $fields = explode('.', $excludedField);
            self::unsetField($fields, $data);
        }

        // rename fields
        foreach (static::$renamed as $oldName => $newName) {
            $data[$newName] = $data[$oldName];
            unset($data[$oldName]);
        }

        if (!empty(static::$only)) {
            $data = array_intersect_key($data, array_flip(static::$only));
        }

        // add included fields
        foreach (static::$modify as $name => $value) {
            if(isset($data[$name]) && is_callable($value)) {
                $data[$name] = call_user_func($value, $data[$name], $this);
            }
        }

        return $data;
    }

    public static function unsetField(array $fields, array &$data): array
    {
        if (sizeof($fields) > 1) {
            self::unsetField(array_slice($fields, 1), $data[$fields[0]]);
        }

        if (sizeof($fields) === 1) {
            unset($data[$fields[0]]);
        }

        return $data;
    }
}
