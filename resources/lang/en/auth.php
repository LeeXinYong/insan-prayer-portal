<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application"s requirements.
    |
    */

    "failed" => "These credentials do not match our records.",
    "password" => "The provided password is incorrect.",
    "throttle" => "Too many login attempts. Please try again in :seconds seconds.",
    "account_suspended" => "Your account has been suspended.",

    "page_title" => [
        "sign_in" => "Sign In",
        "reset_password" => "Reset Password",
    ],

    "legal" => [
        "by_logging_in" => "By logging in, you agree to our",
        "terms_of_service" => "Terms of Service",
        "privacy_policy" => "Privacy Policy",
        "cookie_use" => "Cookie Use",
    ],

    "login" => [
        "continue_with_google" => "Continue with Google",
        "continue_with_facebook" => "Continue with Facebook",
        "create_an_account" => "Create an Account",
        "email" => "Email",
        "forgot_password" => "Forgot Password ?",
        "login_with_password" => "Login with Password",
        "new_here" => "New Here?",
        "password" => "Password",
        "passwordless_login" => "Passwordless Login",
        "sign_in" => "Sign In",
        "sign_in_now" => "Sign In Now",
        "we_have_sent_you_passwordless_login_link" => "We've sent you a passwordless login link to <b>:email</b>. Please click the link to sign in."
    ],

    "reset_password" => [
        "email" => "Email",
        "forgot_password" => "Forgot Password ?",
        "forgot_password_description" => "Enter your email to reset your password.",
        "login" => "Login",
        "password" => "Password",
        "password_confirmation" => "Confirm Password",
        "request" => "Request",
        "success_request" => "We have sent you an email with password reset link.",
        "success_reset" => "Your password has been reset successfully.",
        "suspended_account" => "Your account has been suspended.",
        "reset_your_password" => "Reset your password",
        "reset_your_password_msg" => "This is a secure area of the application. Please confirm your password before continuing..",
        "change_password" => "Change Password",
        "change_password_msg" => "Change password required for first time login",
        "hint" => "Use 8 or more characters with a mix of letters, numbers & symbols."
    ],

    "manage_device" => [
        "trust" => [
            "page_title" => "Trust Device",
            "message" => [
                "title" => "\":device_name\" has been successfully set as trusted!"
            ]
        ],
        "block" => [
            "page_title" => "Block Device",
            "message" => [
                "title" => "Setup New Password",
                "subtitle" => "\":device_name\" has been successfully blocked, please set a new password to secure your account."
            ]
        ]
    ]
];
