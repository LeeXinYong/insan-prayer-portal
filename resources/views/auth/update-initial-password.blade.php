<x-auth-layout>
    <x-slot name="page_title_slot">{{ __("auth.reset_password.change_password") }}</x-slot>

    <!--begin::Reset Password Form-->
    <form method="post" id="change_password_form" class="form w-100" action="{{ route("firsttimelogin.update_password") }}" enctype="multipart/form-data">
        @csrf

        <!--begin::Heading-->
        <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-dark mb-3">
                {{ __("auth.reset_password.change_password") }}
            </h1>
            <!--end::Title-->

            <!--begin::Link-->
            <div class="text-gray-400 fw-bold fs-4">
                {{ __("auth.reset_password.change_password_msg") }}
            </div>
            <!--end::Link-->
        </div>
        <!--begin::Heading-->

        <!--begin::Input group-->
        <div class="mb-10 fv-row" data-kt-password-meter="true">
            <label class="form-label fw-bolder text-gray-900 fs-6">{{ __("auth.reset_password.password") }}</label>
            <input class="form-control form-control-solid" type="password" name="password" id="password" autocomplete="off" required/>
            <div class="pw-validator" style="display: none; color: red"></div>
        </div>
        <!--end::Input group--->

        <!--begin::Input group-->
        <div class="fv-row mb-10">
            <label class="form-label fw-bolder text-gray-900 fs-6">{{ __("auth.reset_password.password_confirmation") }}</label>
            <input class="form-control form-control-solid" type="password" name="password_confirmation" id="password_confirmation" autocomplete="off" required/>
            <div class="confirm-pw-validator" style="display: none; color: red"></div>
        </div>
        <!--end::Input group-->

        <!--begin::Actions-->
        <div class="d-flex flex-wrap justify-content-center pb-lg-0">
            <button type="submit" id="submit_btn" class="btn btn-lg btn-primary fw-bolder me-4">
                @include("partials.general._button-indicator", ["label" => __("general.button.go_to_dashboard"), "message" => __("general.button.saving")])
            </button>
        </div>
        <!--end::Actions-->
    </form>
    <!--end::Reset Password Form-->

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
                    } else {
                        $(this).removeClass("is-invalid").addClass("is-valid");
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
                    let errorMsg = {};
                    errorMsg["match"] = "Password does not match";

                    if($(this).val() === $("#password").val()) {
                        delete errorMsg["match"];
                        $(this).removeClass("is-invalid").addClass("is-valid");
                        if($(".pw-validator").find("li").length === 0) {
                            $("#submit_btn").prop("disabled", false);
                        }
                    } else {
                        $(this).removeClass("is-valid").addClass("is-invalid");
                        $("#submit_btn").prop("disabled", true);
                    }

                    let list = "";
                    $.each(errorMsg, function(index, value) {
                        list += "<li>"+value+"</li>";
                    });
                    $(".confirm-pw-validator").empty().append("<ul>"+list+"</ul>");

                }).focus(function() {
                    $(".confirm-pw-validator").show();
                })

                // Private variables
                let form = $("#change_password_form");
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
                                icon: "success",
                                allowOutsideClick: false,
                                showCancelButton: false,
                                confirmButtonText: response.data.button ?? "{{ __("auth.reset_password.login") }}",
                                customClass: {
                                    confirmButton: "btn btn-primary"
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
        </script>
    @endpush

</x-auth-layout>
