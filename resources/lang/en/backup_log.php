<?php

return [
    // Page Title
    "page_title" => [
        "index" => "Backup Log",
    ],

    "destination_status" => [
        "tabs" => [
            "backup_destinations" => "Backup Destinations",
            "unhealthy_destinations" => "Unhealthy Destinations"
        ],
        "table_header" => [
            "Name" => "Name",
            "Disk" => "Disk",
            "Reachable" => "Reachable",
            "Healthy" => "Healthy",
            "# of backups" => "# of backups",
            "Newest backup" => "Newest backup",
            "Used storage" => "Used storage",
            "Failed check" => "Failed check",
            "Description" => "Description",
        ],
    ],

    "log" => [
        "table_header" => [
            "event" => "Event",
            "action" => "Action",
            "date_and_time" => "Date and Time",
            "disk_name" => "Disk",
            "backup_name" => "Backup Name",
            "message" => "Message",
            "status" => "Status",
            "stack_trace" => "Stack Trace",
        ],
    ],

    "message" => [
        "no_backup_destinations_found" => "No backup destinations found",
        "no_unhealthy_destinations_found" => "No unhealthy destinations found",
        "change_password" => "Change Password",
        "create_banner" => "Create New Banner",
        "create_user" => "Create New User",
        "delete_banner" => "Delete Banner",
        "reactivate_user" => "Reactivate User Account",
        "reset_password" => "Reset Password",
        "suspend_user" => "Suspend User Account",
        "update_banner" => "Update Banner",
        "update_user" => "Update User",
        "create_email_server" => "Create Email Server",
        "update_email_server" => "Update Email Server",
        "update_email_template" => "Update Email Template",
        "send_test_email" => "Send Test Email",
        "send_magic_link" => "Send Magic Link",
        "update_general_setting_timeout" => "Toggle Timeout",
        "update_general_setting_timeout_duration" => "Update Timeout Duration",
        "update_general_setting_timeout_countdown" => "Update Timeout Countdown",
        "update_general_setting_recaptcha" => "Toggle reCAPTCHA",
        "update_general_setting_recaptcha_max_attempt" => "Update Max Login Attempts",
    ],
    "module" => [
        "Banner" => "Banner",
        "User" => "User",
        "EmailServer" => "Email Server",
        "EmailTemplate" => "Email Template",
        "SysParam" => "General Settings",
    ]
];
