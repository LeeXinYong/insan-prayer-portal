<x-base-layout>
    <x-slot name="page_title_slot">{{ __("settings.email_template.page_title.edit") }}</x-slot>
    <!--begin::Form-->
    <form method="post" id="edit_emailtemplate_form" action="{{ route("system.settings.emailtemplate.update", ["emailtemplate" => $emailtemplate->id]) }}" enctype="multipart/form-data">
        @csrf
        @method("PUT")

        <!--begin::Card-->
        <div class="card">
            <div class="card-body">
                @include("pages.common-components._alert-dialog-hint", ["message" => $hint_message])
                <div class="row">
                    <div class="col-6">
                        <label class="col-form-label">{{ __("settings.email_template.form_label.email_subject", []) }}</label>
                        <input type="text" class="form-control" name="subject" value="{{ $emailtemplate->subject }}" required/>
                    </div>
                </div>

                <div class="row mt-10">
                    <div class=col-12"">
                        <label class="col-form-label" for="html_content">{{ __("settings.email_template.form_label.email_contents", []) }}</label>
                        <!-- fields buttons -->
                        <br>
                        @foreach (json_decode($emailtemplate->fields, true) as $key => $value)
                            @include("pages.common-components.buttons.hover-buttons.hover-button", [
                                "size" => "",
                                "classes" => "btn-outline text-dark fields",
                                "attributes" => "tag=@{{{$value}}}",
                                "label" => $key
                            ])
                        @endforeach
                        <br><br>
                        <textarea class="form-control" name="html_content" id="html_content" required>{!! $emailtemplate->html_content !!}</textarea>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex align-items-center justify-content-end gap-3">
                @include("pages.common-components.buttons.cancel-button", [
                    "classes" => "btn-outline"
                ])
                @include("pages.common-components.buttons.test-button", [
                    "indicator" => true,
                    "id" => "testEmailBtn",
                    "label" => __("settings.email_template.button.test_email"),
                    "message" => __("settings.email_template.button.testing")
                ])
                @include("pages.common-components.buttons.save-button", [
                    "indicator" => true
                ])
            </div>
        </div>
        <!--end::Card-->
    </form>

    <form method="post" id="emailPreviewTestForm" action="{{ route("system.settings.emailtemplate.test", ["emailtemplate" => $emailtemplate->id]) }}">
        @csrf
        <textarea name="current_html_content" id="current_html_content" style="display:none"></textarea>
    </form>
    <!--end::Form-->

    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function() {

                initWYSIWYG("#html_content");

                initFormSubmission(
                    $("#edit_emailtemplate_form"),
                    "{{ __("layout.spinner.saving") }}",
                    "{{ __("settings.email_template.message.fail_update") }}"
                )

                $(document).on("click", ".fields", function () {
                    event.preventDefault();
                    var tag = $(this).attr('tag');
                    insertAtCaret(tag);
                });

                function insertAtCaret(text) {
                    tinyMCE.get('html_content').execCommand('mceInsertContent', true, text);
                }

                $("#testEmailBtn").click(function () {
                    var form = $('#emailPreviewTestForm');
                    var submitButton = $(this);

                    swal.fire({
                        title: "{{ __("settings.email_template.message.test_email_prompt", []) }}",
                        text: "{{ __("settings.email_template.message.test_email_prompt_text", []) }}",
                        showCancelButton: true,
                        reverseButtons: true,
                        buttonsStyling: false,
                        confirmButtonText: "{{ __("general.button.send") }}",
                        customClass: {
                            popup: "swal2-info",
                            confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                            cancelButton: "btn btn-custom-light btn-active-custom-light min-w-60px min-w-lg-150px min-h-40px rounded-1 btn-outline"
                        }
                    }).then(function (e) {
                        if (e.value) {

                            // Show loading indication
                            submitButton.attr("data-kt-indicator", "on");

                            // Disable submit button
                            submitButton.prop("disabled", true);

                            // Show spinner
                            SpinnerSingletonFactory.block('{{ __("layout.spinner.sending") }}');

                            // Set latest template contents for preview
                            $('#current_html_content').html(tinymce.get("html_content").getContent());

                            // Send ajax request
                            axios.post(form.prop("action"), new FormData(form[0]))
                                .then(function (response) {
                                    // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                                    Swal.fire({
                                        title: response.data.success,
                                        allowOutsideClick: false,
                                        showCancelButton: false,
                                        confirmButtonText: response.data.button,
                                        buttonsStyling: false,
                                        customClass: {
                                            popup: "swal2-success",
                                            confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
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
                                    let res;
                                    let errors = "";
                                    let swaltitle = error_swal_title;
                                    $.each(error.response.data.errors, function(attribute, error) {
                                        $.each(error, function(index, value) {
                                            // get SWAL title if any
                                            const re = /SWAL_TITLE:s*([^;]*)/gi;
                                            if ((res = re.exec(value)) !== null) {
                                                swaltitle = (res.length > 1) ? res[1] : swaltitle;
                                            } else {
                                                errors += "<li><b>" + value + "</b></li>";
                                            }
                                        });
                                    });

                                    Swal.close();
                                    Swal.fire({
                                        title: swaltitle,
                                        allowOutsideClick: false,
                                        showCancelButton: false,
                                        buttonsStyling: false,
                                        customClass: {
                                            popup: "swal2-danger",
                                            confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                                        },
                                        html:
                                            `<ul class="text-danger text-start">` + errors + `</ul>`,
                                    });
                                })
                                .then(function () {
                                    // always executed
                                    // Hide loading indication
                                    submitButton.removeAttr("data-kt-indicator");

                                    // Enable submit button
                                    submitButton.prop("disabled", false);

                                    // Hide spinner
                                    SpinnerSingletonFactory.unblock();
                                });
                        }
                    })
                });


            })
        </script>
    @endpush
</x-base-layout>
