<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LocalizedModel extends Model
{
    use HasFactory;

    protected static array $localized_fields = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            self::tempSaveAndUnsetLocalizedField($model);
        });

        static::created(function ($model) {
            self::SaveAndRestoreLocalizedField($model);
        });

        static::updating(function ($model) {
            self::tempSaveAndUnsetLocalizedField($model);
        });

        static::updated(function ($model) {
            self::SaveAndRestoreLocalizedField($model);
        });

        static::saving(function ($model) {
            self::tempSaveAndUnsetLocalizedField($model);
        });

        static::saved(function ($model) {
            self::SaveAndRestoreLocalizedField($model);
        });

        static::deleted(function ($model) {
            self::deleteLocalizedField($model);
        });
    }

    private static function tempSaveAndUnsetLocalizedField($model)
    {
        foreach (array_keys($model->toArray()) as $attribute) {

            $terms = explode("_", $attribute, 3);

            if(sizeof($terms) == 3 && str_starts_with($terms[0], "localized")) {
                self::$localized_fields[$attribute] = $model->attributesToArray()[$attribute];
                unset($model->{$attribute});
            }
        }
    }

    private static function SaveAndRestoreLocalizedField($model)
    {
        foreach (self::$localized_fields as $attribute => $value) {
            $model->{$attribute} = $value;

            $terms = explode("_", $attribute, 3);

            $language_code = $terms[1];
            $field_name = $terms[2];

            LocalizedField::query()->updateOrCreate(
                [
                    "subject_type" => get_class($model),
                    "subject_id" => $model->getKey(),
                    "language_code" => $language_code,
                    "field_name" => $field_name,
                ],
                [
                    "field_value" => json_encode($value),
                ]
            );
        }
    }

    private static function deleteLocalizedField($model)
    {
        // If model not used soft delete, delete all localized fields
        if(!in_array("Illuminate\Database\Eloquent\SoftDeletes", class_uses($model))) {
            LocalizedField::query()->where([
                "subject_type" => get_class($model),
                "subject_id" => $model->getKey()
            ])->delete();
        }
    }

    public function getAttribute($key)
    {
        return self::determineAttributeKey($key, parent::getAttribute($key));
    }

    public function getOriginal($key = null, $default = null)
    {
        return self::determineAttributeKey($key, parent::getOriginal($key, $default));
    }

    public function getRawOriginal($key = null, $default = null)
    {
        return self::determineAttributeKey($key, parent::getRawOriginal($key, $default));
    }

    public function localizedField(): HasMany
    {
        return $this->hasMany(LocalizedField::class, "subject_id", "id")->where("subject_type", $this->getMorphClass());
    }

    // Include localized fields into the model instance's attributes
    public function withLocalizedField(): static
    {
        $this->attributes = array_merge(
            $this->attributes,
            $this->localizedField()
                ->select([
                    DB::raw("CONCAT('localized_', language_code, '_', field_name) AS field_name"),
                    "field_value"
                ])
                ->pluck("field_value", "field_name")
                ->toArray()
        );
        return $this;
    }

    // Join the localized fields to the model query as columns
    public function joinLocalizedField($leftJoinAs = "localized_fields"): Builder
    {
        $localized_fields = LocalizedField::query()
            ->select([
                DB::raw("CONCAT(language_code, '_', field_name) AS field_name"),
            ])
            ->where("subject_type", $this->getMorphClass())
            ->groupBy("language_code", "field_name")
            ->pluck("field_name");

        $localized_data = LocalizedField::query()
            ->select(array_merge(
                ["subject_id"],
                ($localized_fields->isEmpty() ? $this->getDefaultLocalizedFields() : $localized_fields )->map(function ($field_name) {
                    return DB::raw("MAX(CASE WHEN CONCAT(language_code, '_', field_name) = '" . $field_name . "' THEN field_value ELSE '' END) AS " . $field_name);
                })->toArray()
            ))
            ->where("subject_type", $this->getMorphClass())
            ->groupBy("subject_id");

        return $this->newQuery()
            ->leftJoinSub($localized_data, $leftJoinAs, function ($join) use ($leftJoinAs) {
                $join->on("$leftJoinAs.subject_id", "=", (new ($this->getMorphClass()))->getTable() . ".id");
            });
    }

    protected function determineAttributeKey($key, $original_value)
    {
        $terms = explode("_", $key, 3);

        if(sizeof($terms) == 3 && str_starts_with($terms[0], "localized")) {
            $language_code = $terms[1];
            $field_name = $terms[2];

            $localized_field =  LocalizedField::query()->firstWhere([
                "subject_type" => $this->getMorphClass(),
                "subject_id" => $this->getKey(),
                "language_code" => $language_code,
                "field_name" => $field_name
            ]);
            if($localized_field) {
                return isset($localized_field->field_value) ? json_decode($localized_field->field_value) : $original_value;
            } else {
                return $original_value;
            }
        } else {
            return $original_value;
        }
    }

    protected function getDefaultLocalizedFields(): Collection
    {
        return collect([]);
    }
}
