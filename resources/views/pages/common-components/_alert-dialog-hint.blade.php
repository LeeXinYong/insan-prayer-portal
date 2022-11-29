<!--begin::Alert hint-->
<div class="alert bg-light-{{ $color ?? "primary" }} alert-dismissible d-flex align-items-center p-5 {{ $class ?? "mb-5" }}" role="alert">

    <!--begin::Icon-->
    @if(isset($icon))
        <i class="{{ $icon }} fs-2x text-{{ ($iconColor ?? "primary") }} me-4"></i>
    @else
        <i class="fa fa-info-circle fs-2x text-{{ ($iconColor ?? "primary") }} me-4"></i>
    @endif
    <!--end::Icon-->

    <!--begin::Wrapper-->
    <div class="d-flex flex-column pe-0 pe-sm-10 {{ ($fontSize ?? "fs-6") }}">
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
            @if(count(array_unique($errors->all())) > 1)
                <ul class="my-auto">
                    @foreach(array_unique($errors->all()) as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @else
                <span class="my-auto">{{ $errors->first() }}</span>
            @endif
            <!--end::Error-->
        @endif
    </div>
    <!--end::Wrapper-->

    @if($close ?? true)
        <!--begin::Close-->
        @include("pages.common-components.buttons.close-button", [
            "color" => "",
            "size" => "btn-icon",
            "classes" => "position-relative m-sm-0 top-0 end-0 ms-sm-auto justify-content-end",
            "attributes" => "data-bs-dismiss=alert",
            "icon" => "fs-8 ra-cancel",
            "icon_only" => true
        ])
        <!--end::Close-->
    @endif
</div>
<!--end::Alert hint-->
