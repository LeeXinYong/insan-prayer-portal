<a {{ isset($id) ? "id=$id" : "" }} {{ isset($link) ? "href=$link" : "" }} {{ isset($target) ? "target=$target" : "" }} class="btn {{ $color ?? "btn-custom-gradient btn-active-custom-gradient" }} {{ $size ?? "min-w-60px min-w-lg-150px min-h-40px" }} {{ ($round_button ?? false) ? "rounded-6" : "rounded-1" }} {{ ($disabled ?? false) ? "link-button-disabled" : "" }} {{ $classes ?? "" }}" {!! $attributes ?? "" !!}>
    @if(($icon_only ?? config("layout.button_icon_enabled")) && !($icon_after ?? false) && is_string($icon ?? null) && !empty($icon ?? null))
        <i class="{{ $icon }} px-0 {{ (!isset($icon_only) || !$icon_only) ? "pe-lg-1" : "" }}"></i>
    @endif
    @if(!($icon_only ?? config("layout.button_icon_enabled")) || !($icon_only ?? false) || !(is_string($icon ?? null) && !empty($icon ?? null)))
        <span class="{{ $label_classes ?? "" }}">{{ $label ?? __("general.button.submit") }}</span>
    @endif
    @if(($icon_only ?? config("layout.button_icon_enabled")) && ($icon_after ?? false) && is_string($icon ?? null) && !empty($icon ?? null))
        <i class="{{ $icon }} px-0 {{ (!isset($icon_only) || !$icon_only) ? "ps-lg-1" : "" }}"></i>
    @endif
</a>
