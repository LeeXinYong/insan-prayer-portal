<?php

return [
    "auth" => env("LAYOUT_AUTH", "aside"),
    "auth_register" => env("LAYOUT_AUTH_REGISTER", false),
    "auth_policy" => env("LAYOUT_AUTH_POLICY", false),
    "auth_aside" => [
        "bg_image" => env("LAYOUT_AUTH_ASIDE_BG_IMAGE", false),
        "illustration" => env("LAYOUT_AUTH_ASIDE_ILLUSTRATION", false),
    ],
    "auth_basic" => [
        "illustration" => env("LAYOUT_AUTH_BASIC_ILLUSTRATION", false),
    ],
    "button_icon_enabled" => env("LAYOUT_BUTTON_ICON_ENABLED", true),
    "demo" => env("LAYOUT_DEMO", "demo3"),
];
