@extends("base.base", ["page_title_slot" => __("auth.manage_device.block.page_title")])

@section("content")
    <div class="container d-flex flex-column mb-25 my-auto mt-0">
        <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-x-center bgi-size-contain bgi-position-y-bottom min-h-100px min-h-lg-500px" style="background-image: url({{ asset("/demo3/customize/media/svg/block-device.svg") }})"></div>

        <div class="w-100 mw-lg-500px mw-xl-550px p-10 pt-0 p-lg-15 pt-lg-0 mx-auto">
            <!--begin::Reset Password Form-->
            <form method="POST" action="{{ route("device.block", ["token" => $token]) }}" class="form w-100" id="reset_password_form">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $token }}">

                <!--begin::Heading-->
                <div class="text-center mb-10">
                    <!--begin::Title-->
                    <h1 class="text-dark fs-2qx text-dark">
                        {{ __("auth.manage_device.block.message.title") }}
                    </h1>
                    <!--end::Title-->

                    <!--begin::Link-->
                    <div class="text-dark fs-4">
                        {{ __("auth.manage_device.block.message.subtitle", ["device_name" => $device_name]) }}
                    </div>
                    <!--end::Link-->
                </div>
                <!--begin::Heading-->

                <!--begin::Input group-->
                <div class="mb-10 fv-row" data-kt-password-meter="true">
                    <!--begin::Wrapper-->
                    <div class="mb-1">
                        <!--begin::Input wrapper-->
                        <div class="position-relative mb-3">
                            <input class="form-control rounded-1 py-4 px-6 fs-5" type="password" placeholder="{{ __('auth.reset_password.password') }}" aria-label="{{ __('auth.reset_password.password') }}" autocomplete="off" name="password" id="password" required/>
                        </div>
                        <!--end::Input wrapper-->

                        <!--begin::Meter-->
                        <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                        </div>
                        <!--end::Meter-->
                    </div>
                    <!--end::Wrapper-->

                    <!--begin::Hint-->
                    <div class="text-muted">
                        {{ __("auth.reset_password.hint") }}
                    </div>
                    <!--end::Hint-->

                    <div class="pw-validator" style="display: none; color: red"></div>
                </div>
                <!--end::Input group--->

                <!--begin::Input group-->
                <div class="fv-row mb-10">
                    <input class="form-control rounded-1 py-4 px-6 fs-5" type="password" placeholder="{{ __('auth.reset_password.password_confirmation') }}" aria-label="{{ __('auth.reset_password.password_confirmation') }}" autocomplete="off" name="password_confirmation" id="password_confirmation" required/>
                    <div class="confirm-pw-validator" style="display: none; color: red"></div>
                </div>
                <!--end::Input group-->

                <!--begin::Actions-->
                <div class="d-flex flex-wrap justify-content-center pb-lg-0">
                    @include("pages.common-components.buttons.submit-button", [
                        "indicator" => true,
                        "id" => "submit_btn",
                        "classes" => "w-100",
                        "disabled" => true,
                        "icon" => "",
                        "label" => __("general.button.reset_password"),
                        "message" => __("general.button.resetting_password")
                    ])
                </div>
                <!--end::Actions-->
            </form>
            <!--end::Reset Password Form-->
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            // Password validation
            $("#password").keyup(function() {
                const password = $(this).val();

                let errorMsg = {};
                errorMsg["minLength"] = "At least <strong>8 characters</strong>";
                errorMsg["minLetter"] =  "At least <strong>one letter</strong>";
                errorMsg["isCapital"] = "At least <strong>one capital letter</strong>";
                errorMsg["isNumber"] = "At least <strong>one number</strong>";
                errorMsg["isSpecial"] = "Use <strong>[~,!,@,#,$,%,^,&,*,-,=,.,;,']</strong>";

                //validate the length
                if ( password.length >= 8 ) {
                    delete errorMsg["minLength"];
                }

                //validate letter
                if ( password.match(/[A-z]/) ) {
                    delete errorMsg["minLetter"];
                }

                //validate capital letter
                if ( password.match(/[A-Z]/) ) {
                    delete errorMsg["isCapital"];
                }

                //validate number
                if ( password.match(/\d/) ) {
                    delete errorMsg["isNumber"];
                }

                //validate space
                if ( password.match(/[^a-zA-Z0-9\-\/]/) ) {
                    delete errorMsg["isSpecial"];
                }

                if(Object.keys(errorMsg).length > 0) {
                    $(this).removeClass("is-valid").addClass("is-invalid");
                    $("#submit_btn").prop("disabled", true);
                } else {
                    $(this).removeClass("is-invalid").addClass("is-valid");
                    validatePasswordConfirmation($("#password_confirmation"));
                }

                let list = "";
                $.each(errorMsg, function(index, value) {
                    list += "<li>"+value+"</li>";
                });
                $(".pw-validator").empty().append("<ul>"+list+"</ul>");
            }).focus(function() {
                $(".pw-validator").show();
            });

            $("#password_confirmation").keyup(function() {
                validatePasswordConfirmation($(this));
            }).focus(function() {
                $(".confirm-pw-validator").show();
            })

            // Private variables
            let form = $("#reset_password_form");
            let submitButton;

            submitButton = form.find("button[type='submit']");
            submitButton.on("click", function() {
                submitForm(form, submitButton);
            });
        })

        function submitForm(form, submitButton) {
            if ((!form.find("input:invalid").length && !form.find("select:invalid").length)) {
                // Show loading indication
                submitButton.attr("data-kt-indicator", "on");

                // Disable submit button
                submitButton.prop("disabled", true);

                // Show spinner
                SpinnerSingletonFactory.block("{{ __("layout.spinner.resetting") }}");

                // Send ajax request
                axios.post(form.prop("action"), new FormData(form[0]))
                    .then(function (response) {
                        // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                        Swal.fire({
                            title: response.data.message ?? "{{ __("auth.reset_password.success_reset") }}",
                            buttonsStyling: false,
                            allowOutsideClick: false,
                            showCancelButton: false,
                            confirmButtonText: response.data.button ?? "{{ __("auth.reset_password.login") }}",
                            customClass: {
                                popup: "swal2-success",
                                confirmButton:"btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                            }
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                if(response.data.redirect != null && response.data.redirect !== "") {
                                    if(window.location.href === response.data.redirect) {
                                        window.location.reload();
                                    } else {
                                        window.location.replace(response.data.redirect);
                                    }
                                } else {
                                    Swal.close();
                                }
                            }
                        });
                    })
                    .catch(function (error) {
                        console.log(error?.response?.data?.errors);
                        $.each(error?.response?.data?.errors, function(attribute, error) {
                            $.each(error, function(index, value) {
                                toastr.error(value);
                            });
                        });
                    }).then(function () {
                    // Hide loading indication
                    submitButton.removeAttr("data-kt-indicator");

                    // Enable submit button
                    submitButton.prop("disabled", false);

                    // Hide spinner
                    SpinnerSingletonFactory.unblock();
                });
            }
        }

        function validatePasswordConfirmation(element) {
            let errorMsg = {};
            errorMsg["match"] = "Password does not match";

            if(element.val() === $("#password").val()) {
                delete errorMsg["match"];
                element.removeClass("is-invalid").addClass("is-valid");
                if($(".pw-validator").find("li").length === 0) {
                    $("#submit_btn").prop("disabled", false);
                }
            } else {
                element.removeClass("is-valid").addClass("is-invalid");
                $("#submit_btn").prop("disabled", true);
            }

            let list = "";
            $.each(errorMsg, function(index, value) {
                list += "<li>"+value+"</li>";
            });
            $(".confirm-pw-validator").empty().append("<ul>"+list+"</ul>");
        }
    </script>
@endpush
