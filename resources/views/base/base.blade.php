<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}"{!! theme()->printHtmlAttributes("html") !!} {{ theme()->printHtmlClasses("html") }}>
{{-- begin::Head --}}
<head>
    <meta charset="utf-8"/>
    <title>{{ $page_title_slot ?? theme()->getOption('page', 'title') }} | {{ config("app.name") }}</title>
    <meta name="description" content="{{ config("app.name") }} Admin Portal"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="shortcut icon" href="{{ asset(theme()->getDemo() . "/" .theme()->getOption("assets", "favicon")) }}"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- begin::Fonts --}}
    {!! theme()->includeFonts() !!}
    {{-- end::Fonts --}}

    @if (theme()->hasOption("page", "assets/vendors/css"))
        {{-- begin::Page Vendor Stylesheets(used by this page) --}}
        @foreach (array_unique(theme()->getOption("page", "assets/vendors/css")) as $file)
            {!! preloadCss(assetCustom($file)) !!}
        @endforeach
        {{-- end::Page Vendor Stylesheets --}}
    @endif

    @if (theme()->hasOption("page", "assets/custom/css"))
        {{-- begin::Page Custom Stylesheets(used by this page) --}}
        @foreach (array_unique(theme()->getOption("page", "assets/custom/css")) as $file)
            {!! preloadCss(assetCustom($file)) !!}
        @endforeach
        {{-- end::Page Custom Stylesheets --}}
    @endif

    @if (theme()->hasOption("assets", "css"))
        {{-- begin::Global Stylesheets Bundle(used by all pages) --}}
        @foreach (array_unique(theme()->getOption("assets", "css")) as $file)
            @if (str_contains($file, "plugins") !== false)
                {!! preloadCss(assetCustom($file)) !!}
            @else
                <link href="{{ assetCustom($file) }}" rel="stylesheet" type="text/css"/>
            @endif
        @endforeach
        {{-- end::Global Stylesheets Bundle --}}
    @endif

    @if (theme()->getViewMode() === "preview")
        {{ theme()->getView("partials/trackers/_ga-general") }}
        {{ theme()->getView("partials/trackers/_ga-tag-manager-for-head") }}
    @endif

    {!! \Biscolab\ReCaptcha\Facades\ReCaptcha::htmlScriptTagJsApi() !!}

    @stack("styles")
</head>
{{-- end::Head --}}

{{-- begin::Body --}}
<body {!! theme()->printHtmlAttributes("body") !!} {!! in_array(request()->segments()[0] ?? "", ["reset-password", "update-password", "block-device"]) ? "class='bg-body'" : theme()->printHtmlClasses("body") !!} {!! theme()->printCssVariables("body") !!}>

@if (theme()->getOption("layout", "loader/display") === true)
    {{ theme()->getView("layout/_loader") }}
@endif

@yield("content")

{{-- begin::Javascript --}}
@if (theme()->hasOption("assets", "js"))
    {{-- begin::Global Javascript Bundle(used by all pages) --}}
    @foreach (array_unique(theme()->getOption("assets", "js")) as $file)
        <script src="{{ asset(theme()->getDemo() . "/" .$file) }}"></script>
    @endforeach
    {{-- end::Global Javascript Bundle --}}
@endif

@if (theme()->hasOption("page", "assets/vendors/js"))
    {{-- begin::Page Vendors Javascript(used by this page) --}}
    @foreach (array_unique(theme()->getOption("page", "assets/vendors/js")) as $file)
        <script src="{{ asset(theme()->getDemo() . "/" .$file) }}"></script>
    @endforeach
    {{-- end::Page Vendors Javascript --}}
@endif

@if (theme()->hasOption("page", "assets/custom/js"))
    {{-- begin::Page Custom Javascript(used by this page) --}}
    @foreach (array_unique(theme()->getOption("page", "assets/custom/js")) as $file)
        <script src="{{ asset(theme()->getDemo() . "/" .$file) }}"></script>
    @endforeach
    {{-- end::Page Custom Javascript --}}
@endif
{{-- end::Javascript --}}

@if (theme()->getViewMode() === "preview")
    {{ theme()->getView("partials/trackers/_ga-tag-manager-for-body") }}
@endif

<script>
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'; // so that $request->ajax() can return true
    $(document).ready(function() {
        @if (App::environment() != "local" && (\App\Models\SysParam::get('timeout') ?? false) && \Illuminate\Support\Facades\Auth::check())
        var alertTO = $.jTimeout({

            timeoutAfter: {{ (\App\Models\SysParam::get('timeout_duration') ?? config('app.timeout_duration')) + (\App\Models\SysParam::get('timeout_countdown') ?? config('app.timeout_countdown')) }}, // default: 1440
            loginUrl: '/login',
            logoutUrl: '/logoutTimeout/0',
            extendUrl: '/',
            secondsPrior: {{ (\App\Models\SysParam::get('timeout_countdown') ?? config('app.timeout_countdown')) }},

            // extends the session when the mouse is moved
            extendOnMouseMove: false,

            // seconds between extending the session when the mouse is moved
            mouseDebounce: 30,

            onPriorCallback: function(timeout, seconds){
                Swal.fire({
                    title: '{{ __("layout.timeout.title") }}',
                    icon: 'warning',
                    allowOutsideClick: false,
                    showCancelButton: true,
                    customClass:{
                        confirmButton:"btn btn-danger",
                        cancelButton:"btn btn-secondary"
                    },
                    confirmButtonText: '{{ __("layout.timeout.logout") }}',
                    cancelButtonText: '{{ __("layout.timeout.stay_connected") }}',
                    reverseButtons: false,
                    html:
                        '<b>{{ __("layout.timeout.prompt") }}<span class="jTimeout_Countdown">' + seconds + '</span> {{ __("general.datetime.seconds") }}!</b>',
                    didOpen: function (alert) {
                        timeout.startPriorCountdown($('.jTimeout_Countdown'));
                    },
                }).then((result) => {
                    if (result.value) {
                        //logout
                        window.location.href = timeout.options.logoutUrl;
                    } else {
                        // extend session
                        timeout.options.onClickExtend(timeout);
                        $.get(timeout.options.extendUrl);
                        Swal.close();
                    }
                });

            },

            onSessionExtended: function(timeout) {
                Swal.close();
            },

            onTimeout: function(timeout){
                /* Force logout */
                window.location.href = '/logoutTimeout/1';
            },

        });

        $(document).mousemove(function(event){
            // $.jTimeout().setExpiration(60);
            if(!Swal.isVisible()) {
                alertTO.resetExpiration();
                Swal.close();
            }
        });
        @endif
    })
</script>

@stack("scripts")
</body>
{{-- end::Body --}}
</html>
