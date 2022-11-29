<?php

namespace App\Rules;

use App\Models\Changelog;
use App\Services\DateTimeFormatterService;
use Illuminate\Contracts\Validation\Rule;

class VersionReleaseDateValidation implements Rule
{
    private string $model;
    private mixed $version;
    private string $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($model, $version = null)
    {
        $this->model = $model;
        $this->version = $version;
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
        if(isset($this->version)) {
            $versions = explode(".", $this->version);
        } else {
            $versions = [];
        }

        $attribute = trans()->has("$this->model.validation_label." . explode(".", $attribute)[0]) ? __("$this->model.validation_label." . explode(".", $attribute)[0]) : str_replace("_", " ", explode(".", $attribute)[0]);

        if(count($versions) == 3) {
            $latest_change_log = ChangeLog::query()
                ->where("version", "like", (int) $versions[0] . "." . (int) $versions[1] . "%")
                ->orderByRaw("INET_ATON(SUBSTRING_INDEX(CONCAT(version,'.0.0'),'.',3)) desc")
                ->first();

            if (!$latest_change_log) {
                $latest_change_log = ChangeLog::query()
                    ->where("version", "like", (int) $versions[0] . ".%")
                    ->orderByRaw("INET_ATON(SUBSTRING_INDEX(CONCAT(version,'.0.0'),'.',3)) desc")
                    ->first();
            }

            if ($latest_change_log && $latest_change_log->released_at_epoch > DateTimeFormatterService::formatModalDateTimeEpoch($value)) {
                $this->message = __("validation.version_release_date_greater_than_or_equal_to", ["attribute" => $attribute, "last_version" => $latest_change_log->version, "last_version_released_date" => $latest_change_log->released_at]);

                return false;
            }
        }
        return true;
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
