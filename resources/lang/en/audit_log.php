<?php

return [
    // Page Title
    "page_title" => [
        "index" => "Audit Log",
    ],

    "table_header" => [
        "action" => "Action",
        "activity" => "Activity",
        "date_and_time" => "Date and Time",
        "description" => "Description",
        "module" => "Module",
        "properties" => "Properties",
        "subject" => "Subject",
        "user" => "User",
    ],
    "message" => [
        "change_password" => "Change Password",
        "forgot_password" => "Forgot Password",
        "reset_password" => "Reset Password",

        "create_banner" => "Create New Banner",
        "delete_banner" => "Delete Banner",
        "update_banner" => "Update Banner",

        "create_brochure" => "Create New Brochure",
        "delete_brochure" => "Delete Brochure",
        "update_brochure" => "Update Brochure",

        "create_video" => "Create New Video",
        "delete_video" => "Delete Video",
        "update_video" => "Update Video",

        "create_changelog" => "Create New Changelog",
        "delete_changelog" => "Delete Changelog",
        "update_changelog" => "Update Changelog",

        "create_guide" => "Create New Guide",
        "delete_guide" => "Delete Guide",
        "update_guide" => "Update Guide",

        "create_news" => "Create New News",
        "delete_news" => "Delete News",
        "update_news" => "Update News",

        "create_user" => "Create New User",
        "suspend_user" => "Suspend User Account",
        "update_user" => "Update User",
        "reactivate_user" => "Reactivate User Account",

        "create_role" => "Create New Role",
        "delete_role" => "Delete Role",
        "update_role" => "Update Role",

        "create_email_server" => "Create Email Server",
        "update_email_server" => "Update Email Server",

        "update_email_template" => "Update Email Template",

        "send_test_email" => "Send Test Email",
        "send_test_notification" => "Send Test Notification",
        "send_notification" => "Send Notification",
        "send_notification_to_test_recipients" => "Send Notification to Test Recipients",
        "send_magic_link" => "Send Magic Link",

        "become_a_test_recipient" => "Become A Test Recipient",
        "resign_as_test_recipient" => "Resign As Test Recipient",

        "update_general_setting_timeout" => "Toggle Timeout",
        "update_general_setting_timeout_duration" => "Update Timeout Duration",
        "update_general_setting_timeout_countdown" => "Update Timeout Countdown",
        "update_general_setting_recaptcha" => "Toggle reCAPTCHA",
        "update_general_setting_recaptcha_max_attempt" => "Update Max Login Attempts",
        "update_general_setting_failed_job_email_alert" => "Toggle Failed Job Alert to Email",
        "update_general_setting_failed_job_webhook_alert" => "Toggle Failed Job Alert to Webhook(s)",
        "update_general_setting_web_portal_concurrent_login" => "Update Concurrent Login For Web Portal",
        "update_general_setting_mobile_app_concurrent_login" => "Update Concurrent Login For Mobile App",

        "create_failed_job_webhook" => "Create Failed Job Webhook",
        "delete_failed_job_webhook" => "Delete Failed Job Webhook",
        "regenerate_secret_key_failed_job_webhook" => "Regenerate Secret Key",
        "send_test_failed_job_webhook" => "Send Test Webhook",
        
        "update_prayer_time" => "Update Time Slot",
    ],
    "module" => [
        "Banner" => "Banner",
        "Brochure" => "Brochure",
        "Changelog" => "Changelog",
        "EmailServer" => "Email Server",
        "EmailTemplate" => "Email Template",
        "FailedJobWebhook" => "Failed Job Webhook",
        "Guide" => "Guide",
        "News" => "News",
        "PushNotification" => "Push Notification",
        "Role" => "Role",
        "SysParam" => "General Settings",
        "TestRecipient" => "Test Recipient",
        "User" => "User",
        "Video" => "Video",
        "PrayerTime" => "Prayer Time",
    ]
];
