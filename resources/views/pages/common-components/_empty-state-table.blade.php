<!--begin::Empty State-->
<div id="{{ $table . "EmptyState" }}" class="{{ (isset($force_show) && $force_show) ? 'd-flex' : 'd-none' }} flex-column align-items-center justify-content-center justify-content-center p-12 pt-0">

    @if(isset($icon))
        {!! theme()->getSvgIcon($icon, "svg-icon-" . ($color ?? "primary") . "", "h-250px w-250px") !!}
    @endif

    @if(isset($img))
        <img src="{{ $img }}" class="h-280px">
    @endif

    <span class="fs-5 fw-bold mt-2 px-12 text-center {{ isset($sub_message) ? 'mb-5' : 'mb-12' }}">
        <!--begin::Content-->
        {!! ($message ?? __('empty_states.default.content')) !!}
        <!--end::Content-->
    </span>

    @if(isset($sub_message))
        <span class="fs-5 text-muted fw-bold mb-12">
            <!--begin::Content-->
            {!! ($sub_message ?? '') !!}
            <!--end::Content-->
        </span>
    @endif

    @if(isset($url) && $url != null)
        @include("pages.common-components.buttons.add-button", [
            "link" => $url ?? "#",
            "icon" => "fs-3 fa fa-chevron-right",
            "icon_after" => true,
            "label" => ($button_label ?? __('empty_states.default.action'))
        ])
    @endif
</div>
<!--end::Empty State-->
