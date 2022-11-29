<x-auth-layout>
    <x-slot name="page_title_slot">{{ __("auth.page_title.sign_in") }}</x-slot>
    <!--begin::Heading-->
    <div class="text-center mb-10">
        <!--begin::Title-->
        <h1 class="text-dark mb-3">
            {{ __("auth.login.sign_in") }}
        </h1>
        <!--end::Title-->
        @if(config("layout.auth_register"))
        <!--begin::Link-->
        <div class="text-gray-400 fw-bold fs-4">
            {{ __("auth.login.new_here") }}

            <a href="{{ route("register") }}" class="link-primary fw-bolder">
                {{ __("auth.login.create_an_account") }}
            </a>
        </div>
        <!--end::Link-->
        @endif
    </div>
    <!--begin::Heading-->

     @if (session('status'))
        @include("pages.common-components._alert-dialog", ["color" => "success", "message" => session('status'), "icon" => "icons/duotune/general/gen048.svg"])
     @elseif (session('error'))
        @include("pages.common-components._alert-dialog", ["color" => "danger", "message" => session('error'), "icon" => "icons/duotune/general/gen050.svg"])
    @endif

    <!--begin::Login tab-->
    <ul class="w-100 nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder mb-10" role="tablist">
        <!--begin::Nav item-->
        <li class="nav-item mt-2 w-50">
            <a class="nav-link text-active-primary text-hover-primary border-active-primary border-hover-primary mx-0 w-100 justify-content-center text-center active" href="#password_login" role="tab" data-bs-toggle="tab">
                {{ __("auth.login.login_with_password") }}
            </a>
        </li>
        <!--end::Nav item-->
        <!--begin::Nav item-->
        <li class="nav-item mt-2 w-50">
            <a class="nav-link text-active-primary text-hover-primary border-active-primary border-hover-primary mx-0 w-100 justify-content-center text-center" href="#passwordless_login" role="tab" data-bs-toggle="tab">
                {{ __("auth.login.passwordless_login") }}
            </a>
        </li>
        <!--end::Nav item-->
    </ul>
    <!--end::Login tab-->
    <div class="tab-content">
        <div class="tab-pane active" id="password_login" role="tabpanel">
            <!--begin::Sign-in Form-->
            <form method="POST" action="{{ route("login") }}" class="form w-100" id="password_login_form">
                @csrf
                <!--begin::Input group-->
                <div class="fv-row mb-10">
                    <!--begin::Label-->
                    <label class="form-label fs-6 fw-bolder text-dark">{{ __("auth.login.email") }}</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-lg form-control-solid" type="email" name="email" autocomplete="off" value="{{ old("email") }}" required/>
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="fv-row mb-10">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-stack mb-2">
                        <!--begin::Label-->
                        <label class="form-label fw-bolder text-dark fs-6 mb-0">{{ __("auth.login.password") }}</label>
                        <!--end::Label-->
                        @if (\Illuminate\Support\Facades\Route::has("password.request"))
                        <!--begin::Link-->
                        <a href="{{ theme()->getPageUrl("password.request") }}" class="link-primary fs-6 fw-bolder" tabindex="-1">
                            {{ __("auth.login.forgot_password") }}
                        </a>
                        <!--end::Link-->
                        @endif
                    </div>
                    <!--end::Wrapper-->
                    <!--begin::Input-->
                    <input class="form-control form-control-lg form-control-solid" type="password" name="password" autocomplete="off" required/>
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::reCaptcha-->
                <div class="fv-row mb-10 input-group justify-content-center">
                    <span id="recaptcha_box" class="{{ ((\App\Models\SysParam::get('recaptcha') ?? false) && \Illuminate\Support\Facades\App::environment() !== "local" && \App\Models\SysParam::get('recaptcha_max_attempt') <= 0) ? '' : 'd-none' }}">
                        {!! \Biscolab\ReCaptcha\Facades\ReCaptcha::htmlFormSnippet() !!}
                    </span>
                </div>
                <!--end::reCaptcha-->
                <!--begin::Submit button-->
                <button type="submit" class="btn btn-lg btn-primary w-100 mb-5">
                    @include("partials.general._button-indicator", ["label" => __("general.button.sign_in"), "message" => __("general.button.signing_in")])
                </button>
                <!--end::Submit button-->
            </form>
            <!--end::Sign-in Form-->
        </div>
        <div class="tab-pane" id="passwordless_login" role="tabpanel">
            <!--begin::Magic Link Form-->
            <form method="POST" action="{{ route("login.send.magiclink") }}" class="form w-100" id="passwordless_login_form">
                @csrf
                <!--begin::Input group-->
                <div class="fv-row mb-10">
                    <!--begin::Label-->
                    <label class="form-label fs-6 fw-bolder text-dark">{{ __("auth.login.email") }}</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-lg form-control-solid" type="email" name="email" autocomplete="off" value="{{ old("email") }}" required/>
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::Submit button-->
                <button type="submit" class="btn btn-lg btn-primary w-100 mb-5">
                    @include("partials.general._button-indicator", ["label" => __("general.button.send_magic_link"), "message" => __("general.button.sending_magic_link")])
                </button>
                <!--end::Submit button-->
            </form>
            <!--end::Magic Link Form-->
        </div>
    </div>
    @if(config("services.socialite"))
    <!--begin::Socialite-->
    <div class="text-center">
        <!--begin::Separator-->
        <div class="text-center text-muted text-uppercase fw-bolder mb-5">or</div>
        <!--end::Separator-->
        @if(!empty(config("services.google.client_id")) && !empty(config("services.google.client_secret")))
        <!--begin::Google link-->
        <a href="{{ !empty(config("services.google.redirect")) ? config("services.google.redirect") : route("socialite.redirect", ["provider" => "google"]) }}?redirect_uri={{ url()->previous() }}" class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
            <img alt="Logo" src="{{ asset(theme()->getMediaUrlPath() . "svg/brand-logos/google-icon.svg") }}" class="h-20px me-3"/>
            {{ __("auth.login.continue_with_google") }}
        </a>
        <!--end::Google link-->
        @endif
        @if(!empty(config("services.facebook.client_id")) && !empty(config("services.facebook.client_secret")))
        <!--begin::Facebook link-->
        <a href="{{ !empty(config("services.facebook.redirect")) ? config("services.google.redirect") : route("socialite.redirect", ["provider" => "facebook"]) }}?redirect_uri={{ url()->previous() }}" class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
            <img alt="Logo" src="{{ asset(theme()->getMediaUrlPath() . "svg/brand-logos/facebook-4.svg") }}" class="h-20px me-3"/>
            {{ __("auth.login.continue_with_facebook") }}
        </a>
        <!--end::Facebook link-->
        @endif
    </div>
    <!--end::Socialite-->
    @endif

    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function() {
                let form = $("#password_login_form");
                let submitButton;

                submitButton = form.find("button[type='submit']");
                submitButton.on("click", function() {
                    submitForm(form, submitButton);
                });

                let passwordlessForm = $("#passwordless_login_form");
                let passwordlessSubmitButton;

                passwordlessSubmitButton = passwordlessForm.find("button[type='submit']");
                passwordlessSubmitButton.on("click", function() {
                    submitForm(passwordlessForm, passwordlessSubmitButton, true);
                });

                function submitForm(form, submitButton, hideSpinnerOnComplete = false) {
                    if ((!form.find("input:invalid").length && !form.find("select:invalid").length)) {
                        // Show loading indication
                        submitButton.attr("data-kt-indicator", "on");

                        // Disable submit button
                        submitButton.prop("disabled", true);

                        // Show spinner
                        SpinnerSingletonFactory.block("{{ __("layout.spinner.signing_in") }}");

                        // Send ajax request
                        axios.post(form.prop("action"), new FormData(form[0]))
                            .then(function (response) {
                                if(response.data.redirect != null) {
                                    window.location.href = response.data.redirect;
                                }

                                if(response.data.message != null) {
                                    toastr.success(response.data.message);
                                }

                                if(hideSpinnerOnComplete) {
                                    $(form).trigger('reset');

                                    // Hide loading indication
                                    submitButton.removeAttr("data-kt-indicator");

                                    // Enable submit button
                                    submitButton.prop("disabled", false);

                                    // Hide spinner
                                    SpinnerSingletonFactory.unblock();
                                }
                            })
                            .catch(function (error) {
                                $.each(error?.response?.data?.errors, function(attribute, error) {
                                    const recaptcha_box = $("#recaptcha_box");
                                    if(attribute === "recaptcha") {
                                        recaptcha_box.removeClass("d-none");
                                    } else if(attribute === "g-recaptcha-response") {
                                        recaptcha_box.removeClass("d-none");
                                        $.each(error, function(index, value) {
                                            toastr.error(value);
                                        });
                                    } else {
                                        $.each(error, function(index, value) {
                                            toastr.error(value);
                                        });
                                    }
                                });

                                // Hide loading indication
                                submitButton.removeAttr("data-kt-indicator");

                                // Enable submit button
                                submitButton.prop("disabled", false);

                                // Hide spinner
                                SpinnerSingletonFactory.unblock();
                            });
                    }
                }
            });
        </script>
    @endpush
</x-auth-layout>
