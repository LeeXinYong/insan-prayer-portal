<!--begin::Container-->
<div id="kt_content_container" class="{{ theme()->printHtmlClasses('content-container', false) }}">
    @include("pages.common-components.buttons.top-back-button")

    @if (\Illuminate\Support\Facades\Session::has("message"))
        @include("pages.common-components._alert-dialog", ["color" => "success", "message" => \Illuminate\Support\Facades\Session::get("message")])
    @endif
    @if (\Illuminate\Support\Facades\Session::has("warning"))
        @include("pages.common-components._alert-dialog", ["color" => "warning", "message" => \Illuminate\Support\Facades\Session::get("warning")])
    @endif
    @if (\Illuminate\Support\Facades\Session::has("errors"))
        @include("pages.common-components._alert-dialog", ["color" => "danger", "errors" => \Illuminate\Support\Facades\Session::get("errors")])
    @endif
    {{ $slot }}
</div>
<!--end::Container-->
