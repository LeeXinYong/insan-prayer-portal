<?php

namespace App\Rules;

use App\Models\Changelog;
use Illuminate\Contracts\Validation\Rule;

class VersionValidation implements Rule
{
    private string $model;
    private string $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if(isset($value)) {
            $versions = explode(".", $value);
        } else {
            $versions = [];
        }

        $attribute = trans()->has("$this->model.validation_label." . explode(".", $attribute)[0]) ? __("$this->model.validation_label." . explode(".", $attribute)[0]) : str_replace("_", " ", explode(".", $attribute)[0]);

        if(count($versions) !=3) {
            $this->message = __("validation.version_format", ["attribute" => $attribute]);
        } else if(!is_numeric($versions[0])) {
            $this->message = __("validation.version_numeric_first", ["attribute" => $attribute]);
        } else if(!is_numeric($versions[1])) {
            $this->message = __("validation.version_numeric_second", ["attribute" => $attribute]);
        } else if(!is_numeric($versions[2])) {
            $this->message = __("validation.version_numeric_third", ["attribute" => $attribute]);
        } else {
            $latest_change_log = ChangeLog::query()
                ->where("version", "like", (int) $versions[0] . "." . (int) $versions[1] . "%")
                ->orderByRaw("INET_ATON(SUBSTRING_INDEX(CONCAT(version,'.0.0'),'.',3)) DESC")
                ->first();

            if (!$latest_change_log) {
                $latest_change_log = ChangeLog::query()
                    ->where("version", "like", (int) $versions[0] . ".%")
                    ->orderByRaw("INET_ATON(SUBSTRING_INDEX(CONCAT(version,'.0.0'),'.',3)) DESC")
                    ->first();
            }

            if ($latest_change_log && version_compare($latest_change_log->version, $value, ">=")) {
                $this->message = __("validation.version_greater_than", ["attribute" => $attribute, "last_version" => $latest_change_log->version]);
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->message ?? "";
    }
}
