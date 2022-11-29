@if($indicator ?? false)
    @include("pages.common-components.buttons.indicator-buttons.solid-indicator-button", [
        "id" => $id ?? null,
        "type" => $type ?? null,
        "color" => $color ?? null,
        "size" => $size ?? null,
        "round_button" => $round_button ?? false, // boolean
        "classes" => $classes ?? null,
        "attributes" => "data-kt-stepper-action=next " . ($attributes ?? null),
        "disabled" => $disabled ?? false, // boolean
        "icon" => $icon ?? "fs-3 ra-continue",
        "icon_after" => $icon_after ?? false, // boolean
        "icon_only" => $icon_only ?? null, // boolean
        "label" => $label ?? __("general.button.next"),
        "label_classes" => $label_classes ?? (($icon_only ?? config("layout.button_icon_enabled")) && is_string($icon ?? "fs-3 ra-continue") && !empty($icon ?? "fs-3 ra-continue") ? "d-none d-lg-inline" : null),
        "message" => $message ?? __("general.button.next"),
        "message_classes" => $message_classes ?? (($icon_only ?? config("layout.button_icon_enabled")) && is_string($icon ?? "fs-3 ra-continue") && !empty($icon ?? "fs-3 ra-continue") ? "d-none d-lg-inline" : null)
    ])
@else
    @include("pages.common-components.buttons.solid-buttons.solid-button", [
        "id" => $id ?? null,
        "type" => $type ?? null,
        "color" => $color ?? null,
        "size" => $size ?? null,
        "round_button" => $round_button ?? false, // boolean
        "classes" => $classes ?? null,
        "attributes" => "data-kt-stepper-action=next " . ($attributes ?? null),
        "disabled" => $disabled ?? false, // boolean
        "icon" => $icon ?? "fs-3 ra-continue",
        "icon_after" => $icon_after ?? false, // boolean
        "icon_only" => $icon_only ?? null, // boolean
        "label" => $label ?? __("general.button.next"),
        "label_classes" => $label_classes ?? (($icon_only ?? config("layout.button_icon_enabled")) && is_string($icon ?? "fs-3 ra-continue") && !empty($icon ?? "fs-3 ra-continue") ? "d-none d-lg-inline" : null)
    ])
@endif
