<x-auth-layout>
    <x-slot name="page_title_slot">{{ __("auth.page_title.reset_password") }}</x-slot>
    <!--begin::Forgot Password Form-->
    <form method="POST" action="{{ route("password.email") }}" class="form w-100" id="forgot_password_form">
    @csrf
        <!--begin::Heading-->
        <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-dark mb-3">
                {{ __("auth.reset_password.forgot_password") }}
            </h1>
            <!--end::Title-->
            <!--begin::Link-->
            <div class="text-gray-400 fw-bold fs-4">
                {{ __("auth.reset_password.forgot_password_description") }}
            </div>
            <!--end::Link-->
        </div>
        <!--begin::Heading-->
        <!--begin::Input group-->
        <div class="fv-row mb-10">
            <label class="form-label fw-bolder text-gray-900 fs-6">{{ __("auth.reset_password.email") }}</label>
            <input class="form-control form-control-solid" type="email" name="email" autocomplete="off" value="{{ old("email") }}" required/>
        </div>
        <!--end::Input group-->
        <!--begin::Actions-->
        <div class="d-flex flex-wrap justify-content-center pb-lg-0">
            <button type="submit" id="kt_password_reset_submit" class="btn btn-lg btn-primary fw-bolder me-4">
                @include("partials.general._button-indicator", ["label" => __("general.button.request"), "message" => __("general.button.requesting")])
            </button>

            <a href="{{ theme()->getPageUrl("login") }}" class="btn btn-lg btn-light-primary fw-bolder">{{ __("general.button.cancel") }}</a>
        </div>
        <!--end::Actions-->
    </form>
    <!--end::Forgot Password Form-->

    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function() {
                let form = $("#forgot_password_form");
                let submitButton;

                submitButton = form.find("button[type='submit']");
                submitButton.on("click", function() {
                    submitForm(form, submitButton, true);
                });

                function submitForm(form, submitButton, hideSpinnerOnComplete = false) {
                    if ((!form.find("input:invalid").length && !form.find("select:invalid").length)) {
                        // Show loading indication
                        submitButton.attr("data-kt-indicator", "on");

                        // Disable submit button
                        submitButton.prop("disabled", true);

                        // Show spinner
                        SpinnerSingletonFactory.block("{{ __("layout.spinner.requesting") }}");

                        // Send ajax request
                        axios.post(form.prop("action"), new FormData(form[0]))
                            .then(function (response) {
                                if(response.data.redirect != null) {
                                    if(window.location.href === response.data.redirect) {
                                        window.location.reload();
                                    } else {
                                        window.location.replace(response.data.redirect);
                                    }
                                }

                                if(response.data.message != null) {
                                    toastr.success(response.data.message);
                                }

                                if(hideSpinnerOnComplete) {
                                    $(form).trigger("reset");

                                    // Hide loading indication
                                    submitButton.removeAttr("data-kt-indicator");

                                    // Enable submit button
                                    submitButton.prop("disabled", false);

                                    // Hide spinner
                                    SpinnerSingletonFactory.unblock();
                                }
                            })
                            .catch(function (error) {
                                console.log(error?.response?.data?.errors);
                                $.each(error?.response?.data?.errors, function(attribute, error) {
                                    $.each(error, function(index, value) {
                                        toastr.error(value);
                                    });
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
