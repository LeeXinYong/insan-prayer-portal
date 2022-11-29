<button {{ isset($id) ? "id=$id" : "" }} type="{{ $type ?? "button" }}" class="btn {{ $color ?? "btn-custom-gradient btn-active-custom-gradient" }} {{ $size ?? "min-w-60px min-w-lg-150px min-h-40px" }} {{ ($round_button ?? false) ? "rounded-6" : "rounded-1" }} {{ $classes ?? "" }}" {!! $attributes ?? "" !!} {{ ($disabled ?? false) ? "disabled" : "" }}>
    <span class="indicator-label">
        @if(($icon_only ?? config("layout.button_icon_enabled")) && !($icon_after ?? false) && is_string($icon ?? null) && !empty($icon ?? null))
            <i class="{{ $icon }} px-0 {{ (!isset($icon_only) || !$icon_only) ? "pe-lg-1" : "" }}"></i>
        @endif
        @if(!($icon_only ?? config("layout.button_icon_enabled")) || !($icon_only ?? false) || !(is_string($icon ?? null) && !empty($icon ?? null)))
            <span class="{{ $label_classes ?? "" }}">{{ $label ?? __("general.button.submit") }}</span>
        @endif
        @if(($icon_only ?? config("layout.button_icon_enabled")) && ($icon_after ?? false) && is_string($icon ?? null) && !empty($icon ?? null))
            <i class="{{ $icon }} px-0 {{ (!isset($icon_only) || !$icon_only) ? "ps-lg-1" : "" }}"></i>
        @endif
    </span>
    <span class="indicator-progress">
        <span class="d-inline-flex align-items-center">
            @if(($icon_only ?? config("layout.button_icon_enabled")) && !($icon_after ?? false) && is_string($icon ?? null) && !empty($icon ?? null))
                <span><i class="fs-3 spinner-border spinner-border-sm {{ (!isset($icon_only) || !$icon_only) ? "me-lg-2" : "" }}"></i></span>
            @endif
            @if(!($icon_only ?? config("layout.button_icon_enabled")) || !($icon_only ?? false) || !(is_string($icon ?? null) && !empty($icon ?? null)))
                <span class="{{ $message_classes ?? "" }}">{{ $message ?? __("general.button.submitting") }}</span>
            @endif
            @if(($icon_only ?? config("layout.button_icon_enabled")) && ($icon_after ?? false) && is_string($icon ?? null) && !empty($icon ?? null))
                <span><i class="fs-3 spinner-border spinner-border-sm {{ (!isset($icon_only) || !$icon_only) ? "me-lg-2" : "" }}"></i></span>
            @endif
        </span>
    </span>
</button>
