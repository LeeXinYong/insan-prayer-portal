@extends("base.base")

@section("content")
    <div class="d-flex flex-column flex-root">
        <!--begin::Authentication-->
        <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: {{ config("layout.auth_basic.illustration") ? "url('" . asset(theme()->getIllustrationUrl("14.png")) . "')" : "none" }};">
            <!--begin::Content-->
            <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
                <!--begin::Logo-->
                <span class="mb-12">
                    <img alt="Logo" src="{{ asset(theme()->getCustomizeUrlPath() . "media/logos/brand-logo.png") }}" class="h-60px">
                </span>
                <!--end::Logo-->
                <!--begin::Wrapper-->
                <div class="{{ $wrapperClass ?? "" }} bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                    {{ $slot }}
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Content-->
            <!--begin::Footer-->
            <div class="d-flex flex-center flex-column-auto p-10">
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
        <!--end::Authentication-->
    </div>
@endsection
