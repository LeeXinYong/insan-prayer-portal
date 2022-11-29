<?php

return [
    "index" => [
        "page_title" => "Roles",
        "table_header" => [
            "name" => "Name",
            "number_of_users" => "Total users",
            "number_of_permissions" => "Total permissions",
            "action" => "Action",
        ],
        "table_util" => [
            "you" => "You",
        ],
        "button" => [
            "view_role" => "View Role",
            "view_users" => "View Users",
        ],
        "addRole" => [
            "label" => "Add Role",
            "success" => "Role has been created successfully",
            "error" => "Role could not be created",
            "validator" => [
                "name" => [
                    "required" => "Role name is required",
                ]
            ]
        ],
    ],

    "addOrEditRoleModal" => [
        "label" => [
            "name" => "Name",
            "color" => "Color",
        ]
    ],

    "show" => [
        "page_title" => "View Role",
        "status_switch" => [
            "select_all" => "Select All",
            "deselect_all" => "Deselect All",
            "select_all_permissions" => "Enable all permissions for this role",
            "deselect_all_permissions" => "Disable all permissions for this role",
        ],
        "delete_role" => [
            "label" => "Delete Role",
            "confirm" => "Are you sure you want to delete this role?",
            "success" => "Role has been deleted successfully",
            "error" => "Role could not be deleted",
        ],
        "update_permissions" => [
            "success" => "Permissions have been updated successfully",
            "failed" => "Permissions could not be updated",
        ],
        "editName" => [
            "label" => "Edit Name",
            "success" => "Role name has been updated",
            "error" => "Role name could not be updated",
            "validator" => [
                "name" => [
                    "required" => "Role name is required",
                ]
            ]
        ],
    ],

    "permissions" => [
        "permission_mapping" => [
            "viewAny" => "View All",
            "view" => "View",
            "create" => "Create",
            "update" => "Update",
            "delete" => "Delete",
            "restore" => "Restore",
            "forceDelete" => "Force Delete",
            "arrange" => "Arrange",
        ],
        "permission_modules" => [
            "Banner" => "Banner",
            "Brochure" => "Brochure",
            "Guide" => "Guide",
            "User" => "User",
            "EmailServer" => "Email Server",
            "EmailTemplate" => "Email Template",
            "SysParam" => "General Settings",
            "SystemLog" => "System Log",
            "Activity" => "Audit Log",
            "BackupLog" => "Backup Log",
            "PushNotification" => "Push Notification",
            "News" => "News",
            "TestRecipient" => "Test Recipient",
            "FailedJobLog" => "Failed Job Log",
            "FailedJobWebhook" => "Failed Job Webhook",
        ],
        "batch_change" => [
            "permissions_granted" => "Permissions granted successfully",
            "failed_to_grant_permissions" => "Failed to grant permissions",
            "permissions_revoked" => "Permissions revoked successfully",
            "failed_to_revoke_permissions" => "Failed to revoke permissions",
        ],
        "change_permission" => [
            "permission_granted" => "Permission granted successfully",
            "failed_to_grant_permission" => "Failed to grant permission",
            "permission_revoked" => "Permission revoked successfully",
            "failed_to_revoke_permission" => "Failed to revoke permission",
        ],
    ],

    "users" => [
        "page_title" => "Role Users",
        "table_header" => [
            "granted" => "Granted",
            "name" => "Name",
            "email" => "Email",
        ],
        "change_user" => [
            "grant" => "Grant",
            "revoke" => "Revoke",
            "grant_role" => "Grant Role",
            "revoke_role" => "Revoke Role",
            "are_you_sure_to_grant_role" => "Are you sure you would like to grant this role to this user?",
            "are_you_sure_to_revoke_role" => "Are you sure you would like to revoke this role from this user?",
            "granting_role" => "Granting role to user",
            "revoking_role" => "Revoking role to user",
            "role_granted" => "Role granted to user successfully.",
            "failed_to_grant_role" => "Failed to grant role to user.",
            "role_revoked" => "Role revoked from user successfully.",
            "failed_to_revoke_role" => "Failed to revoke role from user.",
        ],
    ],

    "delete" => [
        "success" => "Role <strong>:name</strong> deleted successfully",
        "failed" => "Failed to delete role",
        "protected" => "This role is protected and cannot be deleted",
    ],


    "validation_label" => [
        "name" => "name",
    ],
];
