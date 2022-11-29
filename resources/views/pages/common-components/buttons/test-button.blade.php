@if($indicator ?? false)
    @include("pages.common-components.buttons.indicator-buttons.solid-indicator-button", [
        "id" => $id ?? null,
        "type" => $type ?? null,
        "color" => $color ?? "btn-custom-warning btn-active-custom-warning",
        "size" => $size ?? null,
        "round_button" => $round_button ?? false, // boolean
        "classes" => $classes ?? null,
        "attributes" => $attributes ?? null,
        "disabled" => $disabled ?? false, // boolean
        "icon" => $icon ?? "fs-3 ra-test",
        "icon_after" => $icon_after ?? false, // boolean
        "icon_only" => $icon_only ?? null, // boolean
        "label" => $label ?? __("general.button.test"),
        "label_classes" => $label_classes ?? (($icon_only ?? config("layout.button_icon_enabled")) && is_string($icon ?? "fs-3 ra-test") && !empty($icon ?? "fs-3 ra-test") ? "d-none d-lg-inline" : null),
        "message" => $message ?? __("general.button.testing"),
        "message_classes" => $message_classes ?? (($icon_only ?? config("layout.button_icon_enabled")) && is_string($icon ?? "fs-3 ra-test") && !empty($icon ?? "fs-3 ra-test") ? "d-none d-lg-inline" : null)
    ])
@elseif(isset($link))
    @include("pages.common-components.buttons.solid-buttons.solid-link-button", [
        "id" => $id ?? null,
        "link" => $link ?? null,
        "target" => $target ?? null,
        "color" => $color ?? "btn-custom-warning btn-active-custom-warning",
        "size" => $size ?? null,
        "round_button" => $round_button ?? false, // boolean
        "classes" => $classes ?? null,
        "attributes" => $attributes ?? null,
        "disabled" => $disabled ?? false, // boolean
        "icon" => $icon ?? "fs-3 ra-test",
        "icon_after" => $icon_after ?? false, // boolean
        "icon_only" => $icon_only ?? null, // boolean
        "label" => $label ?? __("general.button.test"),
        "label_classes" => $label_classes ?? (($icon_only ?? config("layout.button_icon_enabled")) && is_string($icon ?? "fs-3 ra-test") && !empty($icon ?? "fs-3 ra-test") ? "d-none d-lg-inline" : null)
    ])
@else
    @include("pages.common-components.buttons.solid-buttons.solid-button", [
        "id" => $id ?? null,
        "type" => $type ?? null,
        "color" => $color ?? "btn-custom-warning btn-active-custom-warning",
        "size" => $size ?? null,
        "round_button" => $round_button ?? false, // boolean
        "classes" => $classes ?? null,
        "attributes" => $attributes ?? null,
        "disabled" => $disabled ?? false, // boolean
        "icon" => $icon ?? "fs-3 ra-test",
        "icon_after" => $icon_after ?? false, // boolean
        "icon_only" => $icon_only ?? null, // boolean
        "label" => $label ?? __("general.button.test"),
        "label_classes" => $label_classes ?? (($icon_only ?? config("layout.button_icon_enabled")) && is_string($icon ?? "fs-3 ra-test") && !empty($icon ?? "fs-3 ra-test") ? "d-none d-lg-inline" : null)
    ])
@endif