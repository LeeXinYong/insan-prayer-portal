<?php

use App\Core\Adapters\Menu;

return [
    // Main menu
    "main" => [
        //// Dashboard
        [
            "title" => "menu.dashboard",
            "path" => "",
            "active_path" => [""],
            "icon" => theme()->getSvgIcon("demo3/media/icons/duotune/art/art002.svg", "svg-icon-2"),
        ],

        //// Modules
        [
            "classes" => ["content" => "pt-8 pb-2"],
            "content" => [
                "label" => "menu.modules",
                "classes" => "menu-section text-muted text-uppercase fs-8 ls-1"
            ],
            "hide" => [Menu::class, "shouldHideModulesTag"]
        ],

        // Timeslot
        [
            "title" => "menu.prayer_time",
            "path" => "prayer_time.index",
            "active_path" => ["prayer_time.index"],
            "icon" => "<i class='bi bi-calendar-week-fill fs-3'></i>",
        ],

        // Zone
        [
            "title" => "menu.zone",
            "path" => "zone.index",
            "active_path" => ["zone.index"],
            "icon" => "<i class='bi bi-geo-alt-fill fs-3'></i>",
        ],

        // // Banner
        // [
        //     "title" => "menu.banner",
        //     "path" => "banner.index",
        //     "active_path" => ["banner.index"],
        //     "icon" => "<i class='bi bi-image fs-3'></i>",
        // ],

        // // Brochure
        // [
        //     "title" => "menu.brochure",
        //     "path" => "brochure.index",
        //     "active_path" => ["brochure.index"],
        //     "icon" => "<i class='bi bi-file-earmark-richtext fs-3'></i>",
        // ],

        // // News
        // [
        //     "title" => "menu.news",
        //     "path" => "news.index",
        //     "active_path" => ["news.index"],
        //     "icon" => "<i class='bi bi-newspaper fs-3'></i>",
        // ],

        // // Notifications
        // [
        //     "title" => "menu.notifications",
        //     "icon" => "<i class='bi bi-bell fs-3'></i>",
        //     "classes" => [
        //         "item" => "menu-accordion"
        //     ],
        //     "attributes" => [
        //         "data-kt-menu-trigger" => "click",
        //     ],
        //     "sub" => [
        //         "class" => "menu-sub-accordion menu-active-bg",
        //         "items" => [
        //             [
        //                 "title" => "menu.push_notification",
        //                 "path" => "notification.index",
        //                 "active_path" => ["notification.index", "notification.create"],
        //                 "bullet" => "<span class='bullet bullet-dot'></span>",
        //             ],
        //             [
        //                 "title" => "menu.manage_test_recipient",
        //                 "path" => "notification.testRecipients.index",
        //                 "active_path" => ["notification.testRecipients.index"],
        //                 "bullet" => "<span class='bullet bullet-dot'></span>",
        //             ],
        //         ],
        //     ],
        // ],

        // // Video
        // [
        //     "title" => "menu.video",
        //     "path" => "video.index",
        //     "active_path" => ["video.index"],
        //     "icon" => "<i class='bi bi-youtube fs-3'></i>",
        // ],

        //// Admin
        [
            "classes" => ["content" => "pt-8 pb-2"],
            "content" => [
                "label" => "menu.admin",
                "classes" => "menu-section text-muted text-uppercase fs-8 ls-1"
            ],
            "hide" => [Menu::class, "shouldHideAdminTag"]
        ],

        // Credential
        [
            "title" => "menu.credential",
            "path" => "credential.index",
            "active_path" => ["credential.index"],
            "icon" => "<i class='bi bi-shield-shaded fs-3'></i>",
        ],

        // IP Whitelist
        [
            "title" => "menu.ip_whitelist",
            "path" => "ip_whitelist.index",
            "active_path" => ["ip_whitelist.index"],
            "icon" => "<i class='bi bi-geo-fill fs-3'></i>",
        ],

        // // User Management
        // [
        //     "title" => "menu.user_management",
        //     "icon" => "<i class='bi bi-person fs-3'></i>",
        //     "classes" => [
        //         "item" => "menu-accordion"
        //     ],
        //     "attributes" => [
        //         "data-kt-menu-trigger" => "click",
        //     ],
        //     "sub" => [
        //         "class" => "menu-sub-accordion menu-active-bg",
        //         "items" => [
        //             [
        //                 "title" => "menu.users",
        //                 "path" => "user.index",
        //                 "active_path" => ["user.index"],
        //                 "bullet" => "<span class='bullet bullet-dot'></span>",
        //             ],
        //             [
        //                 "title" => "menu.roles",
        //                 "path" => "role.index",
        //                 "active_path" => ["role.index"],
        //                 "bullet" => "<span class='bullet bullet-dot'></span>",
        //             ],
        //             [
        //                 "title" => "menu.permissions",
        //                 "path" => "permission.index",
        //                 "active_path" => ["permission.index"],
        //                 "bullet" => "<span class='bullet bullet-dot'></span>",
        //             ],
        //         ]
        //     ]
        // ],

        // // System
        // [
        //     "title" => "menu.system",
        //     "icon" => "<i class='bi bi-laptop fs-3'></i>",
        //     "classes" => ["item" => "menu-accordion"],
        //     "attributes" => [
        //         "data-kt-menu-trigger" => "click",
        //     ],
        //     "sub" => [
        //         "class" => "menu-sub-accordion menu-active-bg",
        //         "items" => [
        //             [
        //                 "title" => "menu.settings",
        //                 "bullet" => "<span class='bullet bullet-dot'></span>",
        //                 "classes" => ["item" => "menu-accordion"],
        //                 "attributes" => [
        //                     "data-kt-menu-trigger" => "click",
        //                 ],
        //                 "sub" => [
        //                     "class" => "menu-sub-accordion menu-active-bg",
        //                     "items" => [
        //                         [
        //                             "title" => "menu.general",
        //                             "path" => "system.settings.general.index",
        //                             "active_path" => ["system.settings.general.index"],
        //                             "bullet" => "<span class='bullet bullet-dot'></span>",
        //                         ],
        //                         [
        //                             "title" => "menu.email_server",
        //                             "path" => "system.settings.emailserver.index",
        //                             "active_path" => ["system.settings.emailserver.index"],
        //                             "bullet" => "<span class='bullet bullet-dot'></span>",
        //                         ],
        //                         [
        //                             "title" => "menu.email_template",
        //                             "path" => "system.settings.emailtemplate.index",
        //                             "active_path" => ["system.settings.emailtemplate.index"],
        //                             "bullet" => "<span class='bullet bullet-dot'></span>",
        //                         ],
        //                         [
        //                             "title" => "menu.failed_job_webhook",
        //                             "path" => "system.settings.failed_job_webhook.index",
        //                             "active_path" => ["system.settings.failed_job_webhook.index"],
        //                             "bullet" => "<span class='bullet bullet-dot'></span>",
        //                         ],
        //                     ],
        //                 ],
        //             ],
        //             [
        //                 "title" => "menu.logs",
        //                 "bullet" => "<span class='bullet bullet-dot'></span>",
        //                 "classes" => ["item" => "menu-accordion"],
        //                 "attributes" => [
        //                     "data-kt-menu-trigger" => "click",
        //                 ],
        //                 "sub" => [
        //                     "class" => "menu-sub-accordion menu-active-bg",
        //                     "items" => [
        //                         [
        //                             "title" => "menu.audit_log",
        //                             "path" => "system.log.audit.index",
        //                             "active_path" => ["system.log.audit.index"],
        //                             "bullet" => "<span class='bullet bullet-dot'></span>",
        //                         ],
        //                         [
        //                             "title" => "menu.backup_log",
        //                             "path" => "system.log.backup.index",
        //                             "active_path" => ["system.log.backup.index"],
        //                             "bullet" => "<span class='bullet bullet-dot'></span>",
        //                         ],
        //                         [
        //                             "title" => "menu.changelog",
        //                             "path" => "system.log.changelog.index",
        //                             "active_path" => ["system.log.changelog.index"],
        //                             "bullet" => "<span class='bullet bullet-dot'></span>",
        //                         ],
        //                         [
        //                             "title" => "menu.download_log",
        //                             "path" => "system.log.download.index",
        //                             "active_path" => ["system.log.download.index"],
        //                             "bullet" => "<span class='bullet bullet-dot'></span>",
        //                         ],
        //                         [
        //                             "title" => "menu.failed_job_log",
        //                             "path" => "system.log.failed_job.index",
        //                             "active_path" => ["system.log.failed_job.index"],
        //                             "bullet" => "<span class='bullet bullet-dot'></span>",
        //                         ],
        //                         [
        //                             "title" => "menu.system_log",
        //                             "path" => "system.log.system.index",
        //                             "active_path" => ["system.log.system.index"],
        //                             "bullet" => "<span class='bullet bullet-dot'></span>",
        //                         ],
        //                     ],
        //                 ],
        //             ],
        //         ],
        //     ],
        // ],

        // //// Support
        // [
        //     "classes" => ["content" => "pt-8 pb-2"],
        //     "content" => [
        //         "label" => "menu.support",
        //         "classes" => "menu-section text-muted text-uppercase fs-8 ls-1"
        //     ],
        //     "hide" => [Menu::class, "shouldHideSupportTag"]
        // ],

        // // Support
        // [
        //     "title" => "menu.support",
        //     "icon" => "<i class='bi bi-question-circle fs-3'></i>",
        //     "classes" => [
        //         "item" => "menu-accordion"
        //     ],
        //     "attributes" => [
        //         "data-kt-menu-trigger" => "click",
        //     ],
        //     "sub" => [
        //         "class" => "menu-sub-accordion menu-active-bg",
        //         "items" => [
        //             [
        //                 "title" => "menu.guide",
        //                 "path" => "guide.index",
        //                 "active_path" => ["guide.index"],
        //                 "bullet" => "<span class='bullet bullet-dot'></span>",
        //             ]
        //         ]
        //     ]
        // ],

        // Separator
        /*[
            "content" => [
                "label" => "",
                "classes" => "separator mx-1 my-4"
            ],
        ],*/
    ],

    // Breadcrumbs
    "breadcrumb" => [
        //// Dashboard
        [
            "title" => "menu.dashboard",
            "path" => "",
        ],

        // Timeslot
        [
            "title" => "menu.prayer_time",
            "path" => "prayer_time.index",
            "sub" => [
                "items" => [
                    [
                        "title" => "menu.new_prayer_time",
                        "path" => "prayer_time.create"
                    ],
                    [
                        "title" => "menu.edit_prayer_time",
                        "path" => "prayer_time.edit",
                        "parameters" => [
                            "prayer_time" => ".+",
                        ],
                    ]
                ],
            ]
        ],

        // Zone
        [
            "title" => "menu.zone",
            "path" => "zone.index",
            "sub" => [
                "items" => [
                    [
                        "title" => "menu.new_zone",
                        "path" => "zone.create"
                    ],
                    [
                        "title" => "menu.edit_zone",
                        "path" => "zone.edit",
                        "parameters" => [
                            "zone" => ".+",
                        ],
                    ]
                ],
            ]
        ],

        // Credential
        [
            "title" => "menu.credential",
            "path" => "credential.index",
            "sub" => [
                "items" => [
                    [
                        "title" => "menu.new_credential",
                        "path" => "credential.create"
                    ],
                    [
                        "title" => "menu.edit_credential",
                        "path" => "credential.edit",
                        "parameters" => [
                            "credential" => ".+",
                        ],
                    ]
                ],
            ]
        ],

        // IP Whitelist
        [
            "title" => "menu.ip_whitelist",
            "path" => "ip_whitelist.index",
            "sub" => [
                "items" => [
                    [
                        "title" => "menu.new_ip_whitelist",
                        "path" => "ip_whitelist.create"
                    ],
                    [
                        "title" => "menu.edit_ip_whitelist",
                        "path" => "ip_whitelist.edit",
                        "parameters" => [
                            "ip_whitelist" => ".+",
                        ],
                    ]
                ],
            ]
        ],

        // Banner
        [
            "title" => "menu.banner",
            "path" => "banner.index",
            "sub" => [
                "items" => [
                    [
                        "title" => "menu.new_banner",
                        "path" => "banner.create"
                    ],
                    [
                        "title" => "menu.edit_banner",
                        "path" => "banner.edit",
                        "parameters" => [
                            "banner" => ".+",
                        ],
                    ],
                    [
                        "title" => "menu.arrange_banner",
                        "path" => "banner.arrange"
                    ]
                ],
            ]
        ],

        // Video
        [
            "title" => "menu.video",
            "path" => "video.index",
            "sub" => [
                "items" => [
                    [
                        "title" => "menu.new_video",
                        "path" => "video.create"
                    ],
                    [
                        "title" => "menu.edit_video",
                        "path" => "video.edit",
                        "parameters" => [
                            "video" => ".+",
                        ],
                    ],
                    [
                        "title" => "menu.arrange_video",
                        "path" => "video.arrange"
                    ]
                ],
            ]
        ],

        // Brochure
        [
            "title" => "menu.brochure",
            "path" => "brochure.index",
            "sub" => [
                "items" => [
                    [
                        "title" => "menu.new_brochure",
                        "path" => "brochure.create"
                    ],
                    [
                        "title" => "menu.edit_brochure",
                        "path" => "brochure.edit",
                        "parameters" => [
                            "brochure" => ".+",
                        ],
                    ],
                    [
                        "title" => "menu.arrange_brochure",
                        "path" => "brochure.arrange"
                    ]
                ],
            ]
        ],

        // Guide
        [
            "title" => "menu.guide",
            "path" => "guide.index",
            "sub" => [
                "items" => [
                    [
                        "title" => "menu.new_guide",
                        "path" => "guide.create"
                    ],
                    [
                        "title" => "menu.edit_guide",
                        "path" => "guide.edit",
                        "parameters" => [
                            "guide" => ".+",
                        ],
                    ],
                    [
                        "title" => "menu.arrange_guide",
                        "path" => "guide.arrange"
                    ]
                ],
            ]
        ],

        // News
        [
            "title" => "menu.news",
            "path" => "news.index",
            "sub" => [
                "items" => [
                    [
                        "title" => "menu.new_news",
                        "path" => "news.create"
                    ],
                    [
                        "title" => "menu.edit_news",
                        "path" => "news.edit",
                        "parameters" => [
                            "news" => ".+",
                        ],
                    ],
                    [
                        "title" => "menu.arrange_news",
                        "path" => "news.arrange"
                    ]
                ],
            ]
        ],

        // Push Notification
        [
            "title" => "menu.notifications",
            "path" => "notification.index",
            "sub" => [
                "items" => [
                    [
                        "title" => "menu.new_notification",
                        "path" => "notification.create"
                    ],
                ]
            ],
        ],

        // Test Recipient
        [
            "title" => "menu.manage_test_recipient",
            "path" => "notification.testRecipients.index",
        ],

        // User
        [
            "title" => "menu.users",
            "path" => "user.index",
            "sub" => [
                "items" => [
                    [
                        "title" => "menu.new_users",
                        "path" => "user.create"
                    ],
                    [
                        "title" => "menu.view_user",
                        "path" => "user.show",
                        "parameters" => [
                            "user" => ".+"
                        ],
                    ],
                ]
            ]
        ],

        // Role
        [
            "title" => "menu.roles",
            "path" => "role.index",
            "sub" => [
                "items" => [
                    [
                        "title" => "menu.view_role",
                        "path" => "role.show",
                        "parameters" => [
                            "role" => ".+"
                        ],
                    ],
                    [
                        "title" => "menu.view_users",
                        "path" => "role.show.users",
                        "parameters" => [
                            "role" => ".+"
                        ],
                    ],
                ]
            ]
        ],

        // Permission
        [
            "title" => "menu.permissions",
            "path" => "permission.index",
        ],

        // System
        [
            "title" => "menu.system",
            "sub" => [
                "items" => [
                    [
                        "title" => "menu.settings",
                        "sub" => [
                            "items" => [
                                [
                                    "title" => "menu.general",
                                    "path" => "system.settings.general.index",
                                ],
                                [
                                    "title" => "menu.email_server",
                                    "path" => "system.settings.emailserver.index",
                                ],
                                [
                                    "title" => "menu.email_template",
                                    "path" => "system.settings.emailtemplate.index",
                                    "sub" => [
                                        "items" => [
                                            [
                                                "title" => "menu.edit_email_template",
                                                "path" => "system.settings.emailtemplate.edit",
                                                "parameters" => [
                                                    "emailtemplate" => ".+",
                                                ],
                                            ],
                                        ],
                                    ]
                                ],
                                [
                                    "title" => "menu.failed_job_webhook",
                                    "path" => "system.settings.failed_job_webhook.index",
                                ],
                            ],
                        ],
                    ],
                    [
                        "title" => "menu.logs",
                        "sub" => [
                            "items" => [
                                [
                                    "title" => "menu.audit_log",
                                    "path" => "system.log.audit.index"
                                ],
                                [
                                    "title" => "menu.backup_log",
                                    "path" => "system.log.backup.index"
                                ],
                                [
                                    "title" => "menu.changelog",
                                    "path" => "system.log.changelog.index",
                                    "sub" => [
                                        "items" => [
                                            [
                                                "title" => "menu.new_changelog",
                                                "path" => "system.log.changelog.create"
                                            ],
                                            [
                                                "title" => "menu.edit_changelog",
                                                "path" => "system.log.changelog.edit",
                                                "parameters" => [
                                                    "changelog" => ".+",
                                                ],
                                            ]
                                        ],
                                    ]
                                ],
                                [
                                    "title" => "menu.download_log",
                                    "path" => "system.log.download.index"
                                ],
                                [
                                    "title" => "menu.failed_job_log",
                                    "path" => "system.log.failed_job.index",
                                ],
                                [
                                    "title" => "menu.system_log",
                                    "path" => "system.log.system.index"
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        // Profile
        [
            "title" => "menu.view_profile",
            "path" => "profile.view",
        ],

        // Change Password
        [
            "title" => "menu.change_password",
            "path" => "profile.changePassword",
        ]
    ]
];
