<?php

return array(
    // Main menu
    "main" => array(
        //// Dashboard
        array(
            "title" => "Dashboard",
            "path"  => "",
            "icon"  => theme()->getSvgIcon("demo3/media/icons/duotune/art/art002.svg", "svg-icon-2"),
        ),

        //// Modules
        array(
            "classes" => array("content" => "pt-8 pb-2"),
            "content" => array(
                "label" => "Modules",
                "classes" => "menu-section text-muted text-uppercase fs-8 ls-1"
            ),
        ),

        // Banner
        array(
            "title" => "banner.page_title.index",
            "path" => "banner.index",
            "active_path" => array("banner.index"),
            "icon" => "<i class='bi bi-image fs-3'></i>",
        ),

        // Portal User
        array(
            "title" => "user.page_title.index",
            "path" => "user.index",
            "active_path" => array("user.index"),
            "icon" => "<i class='bi bi-person fs-3'></i>",
        ),

        // System
        array(
            "title" => "System",
            "icon" => array(
                "svg" => theme()->getSvgIcon("demo3/media/icons/duotune/general/gen025.svg", "svg-icon-2"),
                "font" => "<i class='bi bi-layers fs-3'></i>",
            ),
            "classes" => array("item" => "menu-accordion"),
            "attributes" => array(
                "data-kt-menu-trigger" => "click",
            ),
            "sub" => array(
                "class" => "menu-sub-accordion menu-active-bg",
                "items" => array(
                    array(
                        "title" => "Settings",
                        "path" => "#",
                        "bullet"  => "<span class='bullet bullet-dot'></span>",
                        "attributes" => array(
                            "link" => array(
                                "title" => "Coming soon",
                                "data-bs-toggle" => "tooltip",
                                "data-bs-trigger" => "hover",
                                "data-bs-dismiss" => "click",
                                "data-bs-placement" => "right",
                            ),
                        ),
                    ),
                    array(
                        "title" => "audit_log.page_title.index",
                        "active_path" => array("log.audit.index", "log.system.index"),
                        "bullet" => "<span class='bullet bullet-dot'></span>",
                        "classes" => array("item" => "menu-accordion"),
                        "attributes" => array(
                            "data-kt-menu-trigger" => "click",
                        ),
                        "sub" => array(
                            "class" => "menu-sub-accordion menu-active-bg",
                            "items" => array(
                                array(
                                    "title" => "Settings",
                                    "path" => "#",
                                    "bullet"  => "<span class='bullet bullet-dot'></span>",
                                    "attributes" => array(
                                        "link" => array(
                                            "title" => "Coming soon",
                                            "data-bs-toggle" => "tooltip",
                                            "data-bs-trigger" => "hover",
                                            "data-bs-dismiss" => "click",
                                            "data-bs-placement" => "right",
                                        ),
                                    ),
                                ),
                                array(
                                    "title" => "audit_log.page_title.index",
                                    "path" => "log.audit.index",
                                    "active_path" => array("log.audit.index"),
                                    "bullet" => "<span class='bullet bullet-dot'></span>",
                                ),
                                array(
                                    "title" => "System Log",
                                    "path" => "log.system.index",
                                    "active_path" => array("log.system.index"),
                                    "bullet" => "<span class='bullet bullet-dot'></span>",
                                ),
                            ),
                        ),
                    ),
                    array(
                        "title" => "System Log",
                        "path" => "log.system.index",
                        "active_path" => array("log.system.index"),
                        "bullet" => "<span class='bullet bullet-dot'></span>",
                    ),
                ),
            ),
        ),

        // Separator
        array(
            "content" => array(
                "label" => "",
                "classes" => "separator mx-1 my-4"
            ),
        ),

        // Changelog
        array(
            "title" => "Changelog v".theme()->getVersion(),
            "icon"  => theme()->getSvgIcon("demo3/media/icons/duotune/general/gen005.svg", "svg-icon-2"),
            "path"  => "documentation/getting-started/changelog",
        ),
    ),

    // Breadcrumbs
    "breadcrumb" => array(
        //// Dashboard
        array(
            "title" => "Dashboard",
            "path"  => "",
        ),

        // Banner
        array(
            "title" => "banner.page_title.index",
            "path" => "banner.index",
            "sub" => array(
                "items" => array(
                    array(
                        "title" => "banner.page_title.create",
                        "path" => "banner.create"
                    ),
                    array(
                        "title" => "banner.page_title.edit",
                        "path" => "banner.edit",
                        "parameters" => array(
                            "banner_id" => ".+",
                        ),
                    ),
                    array(
                        "title" => "banner.page_title.arrange",
                        "path" => "banner.arrange"
                    )
                ),
            )
        ),

        // User
        array(
            "title" => "user.page_title.index",
            "path" => "user.index",
            "sub" => array(
                "items" => array(
                    array(
                        "title" => "user.page_title.create",
                        "path" => "user.create"
                    ),
                    array(
                        "title" => "user.page_title.view",
                        "path" => "user.show",
                        "parameters" => array(
                            "user_id" => ".+",
                        ),
                    ),
                ),
            )
        ),

        // Account
        array(
            "title" => "Account",
            "sub" => array(
                "items" => array(
                    array(
                        "title" => "Overview",
                        "path" => "account.overview"
                    ),
                    array(
                        "title" => "Settings",
                        "path" => "account.settings.index"
                    )
                ),
            ),
        ),

        // System
        array(
            "title" => "System",
            "sub" => array(
                "items" => array(
                    array(
                        "title" => "audit_log.page_title.index",
                        "path" => "log.audit.index"
                    ),
                    array(
                        "title" => "System Log",
                        "path" => "log.system.index"
                    ),
                ),
            ),
        ),

        // Profile
        array(
            "title" => "profile.page_title.view",
            "path" => "profile.view",
        ),

        // Change Password
        array(
            "title" => "profile.page_title.change_password",
            "path" => "profile.changePassword",
        )
    )
);
