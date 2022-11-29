@extends('base.base')

@section('content')

    <!--begin::Main-->
    @if (theme()->getOption('layout', 'main/type') === 'blank')
        <div class="d-flex flex-column flex-root">
            {{ $slot }}
        </div>
    @else
        <!--begin::Root-->
        <div class="d-flex flex-column flex-root">
            <!--begin::Page-->
            <div class="page d-flex flex-row flex-column-fluid">
            {{ theme()->getView('layout/aside/_base') }}

            <!--begin::Wrapper-->
                <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                {{ theme()->getView('layout/header/_base') }}

                <!--begin::Content-->
                    <div class="content d-flex flex-column flex-column-fluid pb-6" id="kt_content">
                        {{ theme()->getView('layout/_content', compact('slot')) }}
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Wrapper-->

                @if (theme()->getOption('layout', 'sidebar/display') === true)
                    {{ theme()->getView('layout/sidebar/_base') }}
                @endif
            </div>
            <!--end::Page-->
        </div>
        <!--end::Root-->

        <!--begin::Drawers-->
        {{ theme()->getView('partials/topbar/_activity-drawer') }}
        <!--end::Drawers-->

        <!--begin::Engage-->
        {{ theme()->getView('partials/engage/_main') }}
        <!--end::Engage-->

        @if(theme()->getOption('layout', 'scrolltop/display') === true)
            {{ theme()->getView('layout/_scrolltop') }}
        @endif
    @endif
    <!--end::Main-->

@endsection