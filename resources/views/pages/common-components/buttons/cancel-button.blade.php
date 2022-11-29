@if($indicator ?? false)
    @include("pages.common-components.buttons.indicator-buttons.hover-indicator-button", [
        "id" => $id ?? null,
        "type" => $type ?? null,
        "color" => $color ?? null,
        "size" => $size ?? null,
        "round_button" => $round_button ?? false, // boolean
        "classes" => $classes ?? null,
        "attributes" => $attributes ?? "onclick=history.go(-1)",
        "disabled" => $disabled ?? false, // boolean
        "icon" => $icon ?? "fs-3 ra-cancel",
        "icon_after" => $icon_after ?? false, // boolean
        "icon_only" => $icon_only ?? null, // boolean
        "label" => $label ?? __("general.button.cancel"),
        "label_classes" => $label_classes ?? (($icon_only ?? config("layout.button_icon_enabled")) && is_string($icon ?? "fs-3 ra-cancel") && !empty($icon ?? "fs-3 ra-cancel") ? "d-none d-lg-inline" : null),
        "message" => $message ?? __("general.button.canceling"),
        "message_classes" => $message_classes ?? (($icon_only ?? config("layout.button_icon_enabled")) && is_string($icon ?? "fs-3 ra-cancel") && !empty($icon ?? "fs-3 ra-cancel") ? "d-none d-lg-inline" : null)
    ])
@elseif(isset($link))
    @include("pages.common-components.buttons.hover-buttons.hover-link-button", [
        "id" => $id ?? null,
        "link" => $link ?? "javascript:history.go(-1)",
        "target" => $target ?? null,
        "color" => $color ?? null,
        "size" => $size ?? null,
        "round_button" => $round_button ?? false, // boolean
        "classes" => $classes ?? null,
        "attributes" => $attributes ?? null,
        "disabled" => $disabled ?? false, // boolean
        "icon" => $icon ?? "fs-3 ra-cancel",
        "icon_after" => $icon_after ?? false, // boolean
        "icon_only" => $icon_only ?? null, // boolean
        "label" => $label ?? __("general.button.cancel"),
        "label_classes" => $label_classes ?? (($icon_only ?? config("layout.button_icon_enabled")) && is_string($icon ?? "fs-3 ra-cancel") && !empty($icon ?? "fs-3 ra-cancel") ? "d-none d-lg-inline" : null)
    ])
@else
    @include("pages.common-components.buttons.hover-buttons.hover-button", [
        "id" => $id ?? null,
        "type" => $type ?? null,
        "color" => $color ?? null,
        "size" => $size ?? null,
        "round_button" => $round_button ?? false, // boolean
        "classes" => $classes ?? null,
        "attributes" => $attributes ?? "onclick=history.go(-1)",
        "disabled" => $disabled ?? false, // boolean
        "icon" => $icon ?? "fs-3 ra-cancel",
        "icon_after" => $icon_after ?? false, // boolean
        "icon_only" => $icon_only ?? null, // boolean
        "label" => $label ?? __("general.button.cancel"),
        "label_classes" => $label_classes ?? (($icon_only ?? config("layout.button_icon_enabled")) && is_string($icon ?? "fs-3 ra-cancel") && !empty($icon ?? "fs-3 ra-cancel") ? "d-none d-lg-inline" : null)
    ])
@endif