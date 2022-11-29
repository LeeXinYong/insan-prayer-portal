<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class StripTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    public function all($keys = null): array
    {
        $input = array_replace_recursive($this->input(), $this->allFiles());
        $strip_results = [];

        foreach ($input as $key => $value){
            $strip_results[$key] = $this->strip_script($value);
        }

        if (! $keys) {
            return $strip_results;
        }

        $results = [];

        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            Arr::set($results, $key, Arr::get($strip_results, $key));
        }

        return $results;
    }

    public function get($key, $default = null): UploadedFile|string|array|null
    {
        return $this->strip_script(parent::get($key, $default));
    }

    public function __get($key)
    {
        return $this->strip_script(parent::__get($key));
    }

    private function strip_script($value): array|string|UploadedFile|null
    {
        // If attribute is UploadedFile instance, return original value
        if($value instanceof UploadedFile) {
            return $value;
        }
        return ($value === '' || is_null($value)) ? null :
            ((is_array($value) || (ctype_print($value) && is_file($value))) ? $value : preg_replace('#<script(.*?)>(.*?)</script(.*?)>#is', '', $value));
    }
}
