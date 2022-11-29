<!--begin::Indicator-->
<span class="indicator-label">
    <span class="d-inline-flex align-items-center">
        @if(isset($label_icon))
        <i class="fs-3 {{ $label_icon }}"></i>
        <span class="d-none d-lg-block">{{ $label ?? __("general.button.submit") }}</span>
        @else
        <span>{{ $label ?? __("general.button.submit") }}</span>
        @endif
    </span>
</span>
<span class="indicator-progress">
    <span class="d-inline-flex align-items-center">
        @if(isset($label_icon))
        <i class="fs-3 spinner-border spinner-border-sm me-2"></i>
        <span class="d-none d-lg-block">{{ $message ?? __("general.button.submitting") }}</span>
        @else
        <i class="fs-3 spinner-border spinner-border-sm me-2"></i>
        <span>{{ $message ?? __("general.button.submitting") }}</span>
        @endif
    </span>
</span>
<!--end::Indicator-->
