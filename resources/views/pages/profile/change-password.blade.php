<x-base-layout>
    <x-slot name="page_title_slot">{{ __("profile.page_title.change_password") }}</x-slot>
    <!--begin::Form-->
    <form method="post" id="change_password_form" action="{{ route("profile.updatePassword") }}" enctype="multipart/form-data">
        @csrf
        <!--begin::Card-->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <label class="form-label" for="title">{{ __("profile.form_label.current_password") }}</label>
                        <input type="password" name="current_password" id="current_password" autocomplete="off" class="form-control" required/>
                    </div>
                </div>
                <div class="row mt-7">
                    <div class="col-12">
                        <label class="form-label" for="title">{{ __("profile.form_label.new_password") }}</label>
                        <input type="password" name="new_password" id="new_password" autocomplete="off" class="form-control" required/>
                        <div class="pw-validator" style="display: none; color: red"></div>
                    </div>
                </div>
                <div class="row mt-7">
                    <div class="col-12">
                        <label class="form-label" for="title">{{ __("profile.form_label.new_password_confirmation") }}</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" autocomplete="off" class="form-control" required/>
                        <div class="confirm-pw-validator" style="display: none; color: red"></div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex align-items-center justify-content-end">
                    <div class="d-flex align-items-center justify-content-end gap-4">
                        @include("pages.common-components.buttons.cancel-button", [
                            "classes" => "btn-outline"
                        ])
                        @include("pages.common-components.buttons.save-button", [
                            "indicator" => true,
                            "id" => "submit_btn",
                            "attributes" => "disabled"
                        ])
                    </div>
                </div>
            </div>
        </div>
        <!--end::Card-->
    </form>
    <!--end::Form-->

    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function() {
                // Password validation
                $("#new_password").keyup(function() {
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

                $("#new_password_confirmation").keyup(function() {
                    let errorMsg = {};
                    errorMsg["match"] = "Password does not match";

                    if($(this).val() === $("#new_password").val()) {
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

                initFormSubmission(
                    $("#change_password_form"),
                    "{{ __("layout.spinner.changing") }}",
                    "{{ __("profile.message.fail_change_password") }}"
                )
            })
        </script>
    @endpush
</x-base-layout>
