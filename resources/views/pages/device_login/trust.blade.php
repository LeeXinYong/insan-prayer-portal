@extends("base.base", ["page_title_slot" => __("auth.manage_device.trust.page_title")])

@section("content")
    <div class="container d-flex flex-column text-center mb-25 my-auto">
        <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-x-center bgi-size-contain bgi-position-y-bottom min-h-100px min-h-lg-500px" style="background-image: url({{ asset("/demo3/customize/media/svg/trust-device.svg") }})"></div>

        <h1 class="mt-5 fs-2qx text-dark mb-5">{!! __("auth.manage_device.trust.message.title", ["device_name" => $device_name]) !!}</h1>
    </div>
@endsection
