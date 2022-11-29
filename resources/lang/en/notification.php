<?php

use App\Models\Enums\PushNotificationAction;

return [
    "index" => [
        "page_title" => "Push Notifications",
        "table_header" => [
            "title" => "Title",
            "message" => "Message",
            "notification" => "Notification",
            "action" => "Action",
            "image" => "Image",
            "icon" => "Icon",
            "sent_at" => "Sent At",
        ],
        "thumbnail" => "Thumbnail",
    ],

    "create" => [
        "page_title" => "Create Notification",
        "form_label" => [
            "title" => "Title",
            "message" => "Message",
            "image" => "Image",
            "icon" => "Large Icon (Android Only)",
            "action" => "Action",
        ],
        "actions" => [
            PushNotificationAction::Default->name => "Launch App",
            PushNotificationAction::Video->name => "Launch App and View Video",
        ],
        "button" => [
            "send_to_test_recipients" => "Send to Test Recipients",
            "sending_to_test_recipients" => "Sending to Test Recipients...",
            "view_listing" => "View Listing",
        ],
        "message" => [
            "drag_n_drop" => "Drag & drop here",
            "click_to_select" => "<br>or click to select an image<br>(jpeg, jpg, png | Max size: 2MB)",
            "title_too_long" => "Title exceeds the recommended length of :character characters.",
            "message_too_long" => "Message exceeds the recommended length of :character characters.",
            "you_can_still_send_the_notification" => "You can still send the notification if you want but some text may be truncated on some devices.",
            "are_you_sure_to_send_the_notification" => "Are you sure to send the notification?",
            "are_you_sure_to_send_the_notification_to_test_recipients" => "Are you sure to send the notification to test recipients?",
            "success" => "Notification has been sent successfully.",
            "success_to_test_recipients" => "Notification has been sent to test recipients successfully.",
            "failed_to_send_notification" => "Failed to send notification.",
        ]
    ],

    "manage_test_recipients" => [
        "page_title" => "Manage Test Recipients",
        "are_you_sure_to_make_user_test_recipient" => "Are you sure to make :user as a test recipient?",
        "are_you_sure_to_remove_user_test_recipient" => "Are you sure to remove :user from test recipients?",
        "become_a_test_recipient" => "User :user is now a Test Recipient",
        "remove_from_test_recipients" => "User :user is no longer a Test Recipient",
        "not_a_test_recipient" => "User :user is not a Test Recipient",
        "table_header" => [
            "is_test_recipient" => "Test Recipient",
            "name" => "Name",
            "roles" => "Roles",
        ],
    ]
];
