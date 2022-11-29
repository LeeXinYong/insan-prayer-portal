<?php

return [
    "page_title" => "Settings",

    "general" => [
        "menu_title" => "General",
        "page_title" => "General Settings",

        "form_label" => [
            "timeout_title" => "Timeout",
            "timeout_duration" => "Timeout Duration",
            "timeout_countdown" => "Timeout Countdown",
            "recaptcha_title" => "Google reCAPTCHA",
            "recaptcha_max_login_attempts" => "Max Login Attempts",
            "failed_background_job_title" => "Failed Background Job Monitoring",
            "alert_to_email" => "Alert to Email",
            "alert_to_webhooks" => "Alert to Webhook(s)",
            "concurrent_login_title" => "Concurrent Login",
            "web_portal" => "Web Portal",
            "mobile_app" => "Mobile App"
        ],

        "message" => [
            "in_seconds" => "in seconds",
            "timeout_description" => "Duration (in second) of inactivity before the user will be log out from the admin portal.",
            "recaptcha_description" => "Enable Google reCAPTCHA to protect the Portal from spam, bots and brute force attacks.",
            "failed_background_job_description" => "Sending email of failed background jobs (if any)  the moment it failed to all super admins.",
            "success_update" => "General settings updated",
            "fail_update" => "Failed to update general settings",
            "concurrent_login_description" => "Enable concurrent login to allow user access their account via multiple device at the same time."
        ]
    ],

    "email_server" => [
        "page_title" => "Email Server",

        "button" => [
            "done" => "Done",
        ],

        "form_label" => [
            "send_via" => "Send Via",
            "smtp" => "SMTP",
            "mailgun_api" => "Mailgun API",
            "host" => "Host",
            "port" => "Port",
            "encryption_type" => "Encryption Type",
            "tls" => "TLS",
            "ssl" => "SSL",
            "username" => "Username",
            "password" => "Password",
            "sender_name" => "Sender Name",
            "sender_address" => "Sender Address",

            "mailgun" => [
                "domain" => "Domain",
                "api_key" => "API Key",
            ],
        ],

        "message" => [
            "hint" => "All system emails will be sent via server configured in this page.",
            "fail_update" => "Failed to update email server configuration.",
            "success_update" => "Email server configuration updated.",
        ],

        "validation_label" => [
            "send_via" => "send via",
            "host" => "host",
            "port" => "port",
            "encryption_type" => "encryption type",
            "username" => "username",
            "password" => "password",
            "sender_name" => "sender name",
            "sender_address" => "sender address",
            "domain" => "domain",
            "secret" => "API key",
        ],
    ],

    "email_template" => [
        // Page Title
        "page_title" => [
            "edit" => "Edit Email Template",
            "index" => "Email Templates"
        ],

        // Message Collection
        "button" => [
            "view_listing" => "View Email Template Listing",
            "test_email" => "Test Email",
            "testing" => "Testing...",
        ],

        "form_label" => [
            "email_subject" => "Email Subject",
            "email_contents" => "Email Contents",
        ],

        "message" => [
            "hints" => [
                "Customize the email template to you liking. You can even include some HTML tags.",
                "However, please do not remove or edit the variables which are in the curly braces (eg:<code>@{{name}}</code>) - these will be used by the portal to output the data.",
                "Default font - Roboto 14pt",
            ],
            "info" => [
                "last_updated" => "Last Updated",
                "target_user" => "Target User",
                "description" => "Description",
            ],
            "test_email_prompt" => "Send test email?",
            "test_email_prompt_text" => "System will send you a test email to preview the template.",
            "test_email_sent" => "Email Sent! A test email has been sent to your email address.",
            "fail_update" => "Failed to update email template.",
            "success_update" => ":template template successfully updated."
        ],

        "table_header" => [
            "id" => "ID",
            "email_template" => "Email Template",
            "email_subject" => "Email Subject",
            "last_updated" => "Last Updated",
            "target_user" => "Target User",
            "description" => "Description",
            "action" => "Action",
        ],

        "validation_label" => [
            "email_subject" => "email subject",
            "email_contents" => "email contents",
        ],
    ],

    "failed_job_webhook" => [
        "page_title" => "Failed Job Webhook",
        "table_header" => [
            "webhook_url" => "Webhook URL",
            "last_called" => "Last Called",
            "action" => "Action",
        ],

        "create" => [
            "add_new_webhook" => "Add New Webhook",
            "webhook_url" => "Webhook URL",
            "validation" => [
                "webhook_url" => [
                    "required" => "Webhook URL is required.",
                    "pattern" => "Webhook URL is not valid.",
                ]
            ]
        ],

        "message" => [
            "failed_job_alert" => "Failed Job Alert",
            "failed_job_webhook_test" => "Failed Job Webhook Test",

            "hints" => [
                "When a failed job is detected, the system will send a notification to the webhook URL configured in this page.",
                "Please turn on the <code>Alert to Webhook(s)</code> under <code>Failed Background Job Monitoring</code> in the <a href='/system/settings/general'>General Settings</a> to send the notification.",
                "You can add as many webhook URLs as you want.",
            ],

            "webhook_url_required" => "Webhook URL is required.",
            "webhook_url_invalid" => "Please enter a valid URL.",
            "fail_create" => "Failed to add new webhook.",
            "success_create" => "New webhook added.",

            "send_test_webhook" => "Send test webhook",
            "success_test_webhook" => "Test webhook sent",

            "regenerate_secret_key" => "Regenerate secret key",
            "are_you_sure_to_regenerate_secret_key" => "Are you sure to regenerate your secret key for this webhook? After regeneration, the current secret key will be invalidated",
            "webhook_not_found" => "Webhook Not Found",
            "success_regenerate_secret_key" => "Secret key regenerated.",

            "delete_webhook" => "Delete Webhook",
            "are_you_sure_to_delete_webhook" => "Are you sure to delete this webhook? Failed jobs alert will no longer be send to this webhook after deletion.",
            "fail_delete" => "Failed to delete webhook.",
            "success_delete" => "Webhook deleted."
        ],
    ],

    "apps_about" => [
        'page_title' => 'Apps About',
        'last_updated' => 'Last Updated',
        'updated_by' => ':timestamp by :user',
        'about_panasonic' => "About Panasonic",
        'terms_and_conditions' => "Terms & Conditions",
        'privacy_policy' => "Privacy Policy",
    ]
];
