<?php

namespace App\Services;

use App\Http\Controllers\PushNotificationController;
use App\Models\Enums\PushNotificationAction;
use App\Rules\VersionReleaseDateValidation;
use App\Rules\VersionValidation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ConditionalRules;
use Illuminate\Validation\Rule;
use App\Rules\MatchCurrentPassword;
use Illuminate\Support\Facades\Validator;

class ValidationService
{
    /**
     * Get validator for given request.
     *
     * @param $model
     * @param $type
     * @param null $request
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function getValidator($model, $type, $request = null, array $data = []): \Illuminate\Contracts\Validation\Validator
    {
        if (is_null($request)) {
            $request = request();
        }
        return Validator::make($request->all(), self::getValidationRules($model, $type, request: $request, data: $data), self::getValidationMessages($model, $type, request: $request, data: $data));
    }

    /**
     * Get validation rules.
     *
     * @param $model
     * @param $type
     * @param $request
     * @param array $data
     * @return array
     */
    public static function getValidationRules($model, $type, $request = null, array $data = []): array
    {
        if (is_null($request)) {
            $request = request();
        }

        // For model that use base file upload
        if(in_array($model, ["brochure", "guide"])) {
            $model = "base_file_upload";
        }

        return match ("$model.$type") {
            // Banner validation rules
            "banner.create" => [
                "title" => "required|string|max:255",
                "banner" => "required|file|max:2000|dimensions:width=750,height=440|mimes:jpeg,jpg,png",
                "url" => ($request->has("url_content_switch") ? "required" : "nullable") . "|string|url",
                "content" => (!$request->has("url_content_switch") ? "required" : "nullable") . "|string",
                "status" => "nullable|integer|in:1"
            ],
            "banner.edit" => [
                "title" => "required|string|max:255",
                "banner" => "nullable|file|max:2000|dimensions:width=750,height=440|mimes:jpeg,jpg,png",
                "url" => ($request->has("url_content_switch") ? "required" : "nullable") . "|string|url",
                "content" => (!$request->has("url_content_switch") ? "required" : "nullable") . "|string",
                "status" => "nullable|integer|in:1"
            ],


            // Video validation rules
            "video.create" => [
                "video_title" => "required|string|max:255",
                "video_file" => ($request->video_type == "upload" ? "required" : "nullable") . "|max:50000|mimes:mp4",
                "auto_thumbnail" => ($request->has("thumbnail_switch") ? "required" : "nullable") . "|string",
                "manual_thumbnail" => (!$request->has("thumbnail_switch") ? "required" : "nullable") . "|file|max:50000|mimes:jpeg,jpg,png",
                "youtube_url" => ($request->video_type == "youtube" ? "required" : "nullable")  . "|string|url",
                "duration" => "required|string",
                "status" => "required|integer"
            ],
            "video.edit" => [
                "title" => "required|string|max:255",
                "video_file" => ($request->video_type == "upload" && $request->hasFile("video_file") ? "required" : "nullable") . "|max:50000|mimes:mp4",
                "auto_thumbnail" => ($request->get("new_video_upload") == "1" && $request->has("thumbnail_switch") ? "required" : "nullable") . "|string",
                "manual_thumbnail" => ($request->get("new_video_upload") == "1" && !$request->has("thumbnail_switch") ? "required" : "nullable") . "|file|max:50000|mimes:jpeg,jpg,png",
                "youtube_url" => ($request->video_type == "youtube" ? "required" : "nullable")  . "|string|url",
                "duration" => "required|string",
                "status" => "required|integer"
            ],


            // Push Notification validation rules
            "push_notification.create" => [
                "title" => "required|string|max:255",
                "message" => "required|string|max:255",
                "image" => "nullable|file|max:2000|mimes:jpeg,jpg,png",
                "icon" => "nullable|file|max:2000|mimes:jpeg,jpg,png",
                "action" => ['required', 'string', Rule::in(array_keys(PushNotificationAction::getAdminConfigurationActions()))],
                "action_target" => [
                    Rule::when(
                        function () {
                            return PushNotificationController::getActionTargetForValidation() !== null;
                        },
                        ["required", Rule::in(PushNotificationController::getActionTargetForValidation()?->get()->pluck("id")->toArray() ?? [])],
                        "nullable"
                    )
                ],
            ],


            // Base file upload validation rules
            "base_file_upload.create" => [
                "title" => "required|string|max:255",
                "pdf_file" => "required|file|max:50000|mimes:pdf",
                "auto_thumbnail" => ($request->has("thumbnail_switch") ? "required" : "nullable") . "|string",
                "manual_thumbnail" => (!$request->has("thumbnail_switch") ? "required" : "nullable") . "|file|max:2000|mimes:jpeg,jpg,png",
                "status" => "nullable|integer|in:1"
            ],
            "base_file_upload.edit" => [
                "title" => "required|string|max:255",
                "pdf_file" => "nullable|file|max:50000|mimes:pdf",
                "auto_thumbnail" => ($request->hasFile("pdf_file") && $request->has("thumbnail_switch") ? "required" : "nullable") . "|string",
                "manual_thumbnail" => ($request->hasFile("pdf_file") && !$request->has("thumbnail_switch") ? "required" : "nullable") . "|file|max:2000|mimes:jpeg,jpg,png",
                "status" => "nullable|integer|in:1"
            ],


            // News validation rules
            "news.create" => [
                "title" => "required|string|max:255",
                "thumbnail" => "required|file|max:2000|mimes:jpeg,jpg,png",
                "url" => ($request->has("url_content_switch") ? "required" : "nullable") . "|string|url",
                "content" => (!$request->has("url_content_switch") ? "required" : "nullable") . "|string",
                "status" => "nullable|integer|in:1"
            ],
            "news.edit" => [
                "title" => "required|string|max:255",
                "thumbnail" => "nullable|file|max:2000|mimes:jpeg,jpg,png",
                "url" => ($request->has("url_content_switch") ? "required" : "nullable") . "|string|url",
                "content" => (!$request->has("url_content_switch") ? "required" : "nullable") . "|string",
                "status" => "nullable|integer|in:1"
            ],


            // User validation rules
            "user.create" => [
                "name" => "required|string|max:255",
                "email" => "required|string|email|max:255|unique:users",
                "mobile" => "nullable|string|max:255",
                "country" => "required|string|exists:countries,iso2",
                "timezone" => "required|string|exists:timezones,name",
                "status" => "nullable|integer|in:1"
            ],
            "user.edit" => [
                "name" => "required|string|max:255",
                "email" => "required|string|email|max:255|unique:users,email,{$request->route()->parameter("user.id")}",
                "mobile" => "nullable|string|max:255",
                "country" => "required|string|exists:countries,iso2",
                "timezone" => "required|string|exists:timezones,name"
            ],
            "user.forgot_password" => [
                "email" => "required|string|email|max:255|exists:users,email"
            ],


            // Role validation rules
            "role.edit" => [
                // edit name and color
                "name" => ["required", "string", "max:255", Rule::unique("roles", "name")->ignore($data['id'] ?? "")],
                "color" => ["required", "string", "max:255", function($attribute, $value, $fail) {
                    if (!preg_match("/^#[a-f0-9]{6}$/i", strtolower($value))) {
                        $fail("The color must be a valid hex color code.");
                    }
                }],

//                // edit permission
//                "permissionReference" => "required_without_all:name,userReference,moduleReference|exists:permissions,name",
//                "grantingPermission" => "required_with:permissionReference|boolean",
//
//                // edit all permissions
//                "moduleReference" => "required_without_all:name,permissionReference,userReference",
//                "grantingModulePermissions" => "required_with:moduleReference|boolean",
//
//                // edit user
//                "userReference" => "required_without_all:name,permissionReference,moduleReference|exists:users,id",
//                "grantingUser" => "required_with:userReference|boolean",
            ],

            // Update role permissions
            "role.updatePermissions" => [
                "permissions" => "required|array",
                "permissions.*" => ["required", "string", "exists:permissions,name",
                    function ($attribute, $value, $fail) use ($data) {
                        $role = $data['role'];
                        [$class, $action] = explode("::", $value);
                        if(Auth::user()->cannot("updatePermission", [$role, $class, $action])) {
                            $fail("You do not have permission to update this permission");
                        }
                    }],
            ],

            "role.updateUser" => [
                "userReference" => "required|exists:users,id",
                "grantingUser" => "required|boolean",
            ],

            // Changelog validation rules
            "changelog.create" => [
                "version" => ["required", new VersionValidation($model)],
                "released_by" => "required|string",
                "released_at" => ["required", "date", new VersionReleaseDateValidation($model, $request->get("version"))],
                "description" => "required",
                "description.*" => "required|string",
            ],
            "changelog.edit" => [
                "description" => "required",
                "description.*" => "required|string",
            ],


            // Profile validation rules
            "profile.edit" => [
                "name" => "required|string|max:255",
                "email" => "required|string|email|max:255|unique:users,email," . Auth::user()->id,
                "mobile" => "nullable|string|max:255",
                "country" => "required|string|exists:countries,iso2",
                "timezone" => "required|string|exists:timezones,name"
            ],
            "profile.change-password" => [
                "current_password" => ["required", "string", new MatchCurrentPassword($model)],
                "new_password" => "required|string|confirmed"
            ],
            "profile.change-initial-password" => [
                "password" => "required|string|confirmed"
            ],


            // Email Server validation rules
            "settings.email_server.edit" => [
                'transport' => ['required', 'string', Rule::in(['SMTP', 'MAILGUN_API'])],
                'name' => 'required|string',
                'address' => 'required|string',
                'host' => [Rule::requiredIf($request->transport == "SMTP"), 'nullable', 'string'],
                'port' => [Rule::requiredIf($request->transport == "SMTP"), 'nullable', 'string'],
                'encryption' => [Rule::requiredIf($request->transport == "SMTP"), 'nullable', 'string'],
                'username' => [Rule::requiredIf($request->transport == "SMTP"), 'nullable', 'string'],
                'password' => [Rule::requiredIf($request->transport == "SMTP"), 'nullable', 'string'],
                'domain' => [Rule::requiredIf($request->transport == "MAILGUN_API"), 'nullable', 'string'],
                'secret' => [Rule::requiredIf($request->transport == "MAILGUN_API"), 'nullable', 'string'],
            ],

            // Email Template validation rules
            "settings.email_template.edit" => [
                'subject' => 'required|string|max:100',
                'html_content' => 'required|string',
            ],

            // General Setting validation rules
            "settings.general.edit" => [
                'timeout_duration' => [Rule::requiredIf($request->has('timeout') && $request->get('timeout') === "on"), 'nullable', 'integer'],
                'timeout_countdown' => [Rule::requiredIf($request->has('timeout') && $request->get('timeout') === "on"), 'nullable', 'integer'],
                'recaptcha_max_attempt' => [Rule::requiredIf($request->has('recaptcha') && $request->get('recaptcha') === "on"), 'nullable', 'integer'],
            ],

            "settings.failed_job_webhook.create" => [
                'webhook_url' => 'required|url|unique:failed_job_webhooks,endpoint',
            ],

            default => [],
        };
    }

    /**
     * Get validation messages.
     *
     * @param $model
     * @param $type
     * @param null $request
     * @param array $data
     * @return array
     */

    public static function getValidationMessages($model, $type, $request = null, array $data = []): array
    {
        if (is_null($request)) {
            $request = request();
        }
        $validation_rules = self::getValidationRules($model, $type, request: $request, data: $data);
        $messages = [];
        foreach ($validation_rules as $attribute => $rules) {
            // Check attribute type
            if (is_string($rules)) {
                if (strpos($rules, "|array|")) {
                    $attribute_type = "array";
                } else if (strpos($rules, "|integer|") || strpos($rules, "|numeric|")) {
                    $attribute_type = "numeric";
                } else if (strpos($rules, "|file|")) {
                    $attribute_type = "file";
                } else {
                    $attribute_type = "string";
                }
                // If attribute rules in string, split it to array
                $rules = explode("|", $rules);
            } else if (is_array($rules)) {
                if (in_array("array", $rules)) {
                    $attribute_type = "array";
                } else if (in_array("integer", $rules) || in_array("numeric", $rules)) {
                    $attribute_type = "numeric";
                } else if (in_array("file", $rules)) {
                    $attribute_type = "file";
                } else {
                    $attribute_type = "string";
                }
            } else {
                continue;
            }

            // Get localized validation messages
            $messages = array_merge($messages, self::getLocalizedValidationMessages($model, $attribute, $attribute_type, $rules));
        }
        return $messages;
    }

    private static function getLocalizedValidationMessages($model, $attribute, $attribute_type, $rules): array
    {
        $messages = [];
        $attribute_label = trans()->has("$model.validation_label." . explode(".", $attribute)[0]) ? __("$model.validation_label." . explode(".", $attribute)[0]) : str_replace("_", " ", explode(".", $attribute)[0]);
        foreach ($rules as $rule) {
            if (is_string($rule)) {
                // Split attribute rule with ":" for the rule that can customize
                $rule = explode(":", $rule)[0];

                switch ($rule) {
                    // Rule that has no message
                    case "nullable" :
                        break;
                    // Rule message that different due to attribute type, with customize attribute name
                    case "min" :
                    case "max" :
                        $messages[$attribute . "." . $rule] = __("validation." . $rule . "." . $attribute_type, ["attribute" => $attribute_label]);
                        break;
                    // Rule message, with customize attribute name
                    default :
                        $messages[$attribute . "." . $rule] = __("validation." . $rule, ["attribute" => $attribute_label]);
                        break;
                }
            } else if(is_object($rule)) {
                if($rule instanceof ConditionalRules) {
                    $messages = array_merge($messages, self::getLocalizedValidationMessages($model, $attribute, $attribute_type, $rule->rules()));
                } else if(str_contains(get_class($rule), "Illuminate\Validation\Rules\\")){
                    $rule = strtolower(preg_replace("/([A-Z])/", "_$1", lcfirst(str_replace("Illuminate\Validation\Rules\\", "", get_class($rule)))));

                    switch ($rule) {
                        case "required_if" :
                            $messages[$attribute . ".required"] = __("validation.required" , ["attribute" => $attribute_label]);
                            break;
                        case "dimensions" :
                        case "exists" :
                        case "in" :
                        case "not_in" :
                        case "unique" :
                            $messages[$attribute . "." . $rule] = __("validation." . $rule, ["attribute" => $attribute_label]);
                            break;
                    }
                }
            }
        }

        return $messages;
    }
}
