<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\SysParam;

class LoginRequest extends FormRequest
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
            "email" => "required|string|email",
            "password" => "required|string",
            "g-recaptcha-response" => $this->requiredReCAPTCHA() ? "required" : "",
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            "g-recaptcha-response.required" => __("validation.recaptcha"),
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function authenticate()
    {
        //$this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only("email", "password"), $this->boolean("remember"))) {
            RateLimiter::hit($this->throttleKey());

            if($this->requiredReCAPTCHA()) {
                throw ValidationException::withMessages([
                    "email" => __("auth.failed"),
                    "recaptcha" => true
                ]);
            } else {
                throw ValidationException::withMessages([
                    "email" => __("auth.failed"),
                    "attempt" => "Attempt is " . RateLimiter::attempts($this->throttleKey()),
                ]);
            }
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            "email" => trans("auth.throttle", [
                "seconds" => $seconds,
                "minutes" => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey(): string
    {
        return Str::lower($this->input("email"))."|".$this->ip();
    }

    public function requiredReCAPTCHA()
    {
        $loginAttempts = RateLimiter::attempts($this->throttleKey());
        $maxAttempts = SysParam::get('recaptcha_max_attempt') ?? config('app.max_attempt', 1);
        return (SysParam::get('recaptcha') ?? false) && \Illuminate\Support\Facades\App::environment() !== "local" && ($loginAttempts >= $maxAttempts);
    }
}
