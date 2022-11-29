@extends("base.base")

@section("content")
    <div class="d-flex flex-column flex-root">
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Aside-->
            <div class="d-flex flex-column flex-lg-row-auto w-lg-275px w-xl-600px position-lg-relative bg-light-primary bgi-size-cover bgi-position-center" style="background-image: {{ config("layout.auth_aside.bg_image") ? "url('" . asset(theme()->getCustomizeUrlPath() . "media/background/login-side.jpg") . "')" : "none" }};">
                <!--begin::Wrapper-->
                <div class="d-flex flex-column position-lg-fixed top-0 bottom-0 w-lg-275px w-xl-600px scroll-y">
                    <!--begin::Content-->
                    <div class="d-flex flex-row-fluid flex-column text-center p-10 pt-lg-20 justify-content-center">
                        <!--begin::Logo-->
                        <span class="py-9 mb-5">
                            <img alt="Logo" src="{{ asset(theme()->getCustomizeUrlPath() . "media/logos/brand-logo.png") }}" class="mw-200px w-50">
                        </span>
                        <!--end::Logo-->
                        <!--begin::Title-->
                        <h1 class="fw-bolder fs-2qx pb-5 pb-md-10">{{ config("app.name") }}</h1>
                        <!--end::Title-->
                        @if(trans()->has("general.app.description") && !empty(__("general.app.description")))
                        <!--begin::Description-->
                        <p class="fw-bold fs-2">{{ __("general.app.description") }}</p>
                        <!--end::Description-->
                        @endif
                    </div>
                    <!--end::Content-->
                    @if(config("layout.auth_aside.illustration"))
                    <!--begin::Illustration-->
                    <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-x-center bgi-size-contain bgi-position-y-bottom min-h-100px min-h-lg-350px" style="background-image: url('{{ asset(theme()->getMediaUrlPath() . "illustrations/dozzy-1/13.png") }}');"></div>
                    <!--end::Illustration-->
                    @endif
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Aside-->
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid py-10">
                <!--begin::Content-->
                <div class="d-flex flex-center flex-column flex-column-fluid">
                    <!--begin::Wrapper-->
                    <div class="{{ $wrapperClass ?? "" }} p-10 p-lg-15 mx-auto">
                        {{ $slot }}
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Content-->
                <!--begin::Footer-->
                <div class="d-flex flex-center flex-wrap fs-6 p-5 pb-0">
                    <div class="d-flex flex-column flex-center fw-bold fs-6">
                        <!--begin::Copyright-->
                        <span class="text-muted text-center px-2 ">{{ date("Y") }} &copy; {{ config("app.company_name") }}</span>
                        <!--end::Copyright-->
                        @if(config("layout.auth_policy"))
                        <!--begin::Policy-->
                        <div class="text-muted text-center mt-4 mt-lg-0">
                            {{ __("auth.legal.by_logging_in") }}&nbsp;<a target="_blank" href="{{ \Illuminate\Support\Facades\Route::has("legal.terms_and_conditions") ? route("legal.terms_and_conditions") : "" }}">{{ __("auth.legal.terms_of_service") }}</a>&nbsp;{{ __("general.connectors.and") }}&nbsp;<a target="_blank" href="{{ \Illuminate\Support\Facades\Route::has("legal.privacy") ? route("legal.privacy") : "" }}">{{ __("auth.legal.privacy_policy") }}</a>, {{ __("general.connectors.including") }}&nbsp;<a target="_blank" href="{{ \Illuminate\Support\Facades\Route::has("legal.cookie") ? route("legal.cookie") : "" }}">{{ __("auth.legal.cookie_use") }}</a>.
                        </div>
                        <!--end::Policy-->
                        @endif
                    </div>
                </div>
                <!--end::Footer-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Authentication - Sign-in -->
    </div>
    <!--begin::Authentication-->
    <!--end::Authentication-->
@endsection
