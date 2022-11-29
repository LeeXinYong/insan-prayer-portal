<!--begin::Alert-->
<div class="alert alert-{{ $color ?? "primary" }} alert-dismissible d-flex align-items-center p-5 {{ $class ?? "mb-5" }}">
    <!--begin::Icon-->
    @if(isset($icon))
        {!! theme()->getSvgIcon($icon, "svg-icon-2hx svg-icon-" . ($color ?? "primary") . " me-3") !!}
    @else
        {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg", "svg-icon-2hx svg-icon-" . ($color ?? "primary") . " me-3") !!}
    @endif
    <!--end::Icon-->

    <!--begin::Wrapper-->
    <div class="d-flex flex-column pe-0 pe-sm-10 fs-6">
        @if(isset($title))
            <!--begin::Title-->
            <h4 class="mb-1">{!! $title !!}</h4>
            <!--end::Title-->
        @endif
        @if(isset($message))
            <!--begin::Content-->
            <span>{!! $message !!}</span>
            <!--end::Content-->
        @endif
        @if(isset($errors))
            <!--begin::Error-->
            <ul class="my-auto">
                @foreach(array_unique($errors->all()) as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <!--end::Error-->
        @endif
    </div>
    <!--end::Wrapper-->


    @if($close ?? true)
        <!--begin::Close-->
        @include("pages.common-components.buttons.close-button", [
            "color" => "",
            "size" => "btn-icon",
            "classes" => "text-" . ($color ?? "primary") . " position-relative m-sm-0 top-0 end-0 ms-sm-auto justify-content-end",
            "attributes" => "data-bs-dismiss=alert",
            "icon" => "fs-9 ra-cancel",
            "icon_only" => true
        ])
        <!--end::Close-->
    @endif
</div>
<!--end::Alert-->
