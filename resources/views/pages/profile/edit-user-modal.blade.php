<div class="modal fade" tabindex="-1" id="edit-user-modal" data-bs-focus="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    {{ __('user.page_title.edit') }}
                </h2>

                <!--begin::Close-->
                @include("pages.common-components.buttons.close-button", [
                    "size" => "btn-icon",
                    "attributes" => "data-bs-toggle=modal data-bs-label=close",
                    "icon" => "fs-6 ra-cancel",
                    "icon_only" => true
                ])
                <!--end::Close-->
            </div>

            <div class="modal-body mx-5 my-2">
                <form id="edit_user_form" class="form fv-plugins-bootstrap5 fv-plugins-framework"
                      enctype="multipart/form-data" novalidate>
                    @csrf
                    @method("PUT")
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label" for="name">{{ __("user.form_label.name") }}</label>
                            <input type="text" name="name" id="name" class="form-control"
                                   value="{{ $profile->name }}" required/>
                        </div>
                    </div>
                    <div class="row mt-7">
                        <div class="col-6">
                            <label class="form-label" for="email">{{ __("user.form_label.email") }}</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   value="{{ $profile->email }}" required/>
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="mobile">{{ __("user.form_label.mobile") }} <span
                                    class="fs-9 fst-italic">({{ __("general.form_label.optional") }})</label>
                            <input type="text" name="mobile" id="mobile" class="form-control"
                                   value="{{ $profile->mobile }}"/>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-6">
                            <label class="form-label" for="country">{{ __("user.form_label.country") }}</label>
                            <select name="country" id="country" class="form-select" required>
                                <option value="">{{ __("general.message.please_select") }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->iso2 }}" data-id="{{ $country->id }}" {{ $country->iso2 == $profile->country_code ? "selected" : "" }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="timezone">{{ __("user.form_label.timezone") }}</label>
                            <select name="timezone" id="timezone" class="form-select" required>
                                <option value="">{{ __("general.message.please_select") }}</option>
                            </select>
                            <div class="form-text text-muted">{{ __("user.message.timezone_msg") }}</div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                @include("pages.common-components.buttons.cancel-button", [
                    "classes" => "btn-outline",
                    "attributes" => "data-bs-dismiss=modal"
                ])
                @include("pages.common-components.buttons.save-button", [
                    "indicator" => true,
                    "id" => "update-button"
                ])
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const editUserModalForm = document.getElementById('edit_user_form');
        const updateButton = document.getElementById("update-button");

        $(document).ready(function () {
            // $("#role").select2({
            //     placeholder: "{{ __("general.message.please_select") }}",
            //     allowClear: false,
            //     width: '100%',
            //     minimumResultsForSearch: Infinity,
            // });

            $("#country, #timezone").select2({
                placeholder: "{{ __("general.message.please_select") }}",
                allowClear: false,
                width: '100%',
            });

            $("#country").change(function () {
                getCountryTimezone(
                    "{{ route("getCountryTimezone", ["country_id" => ":id"]) }}".replace(":id", $(this).children("option:selected").data("id")),
                    "{{ __("layout.spinner.retrieving") }}",
                    "{{ $profile->timezone }}"
                );
            }).trigger("change");

            const editUserDetailModalElement = document.getElementById("edit-user-modal");
            const editUserDetailModal = bootstrap.Modal.getOrCreateInstance(editUserDetailModalElement);

            editUserDetailModalElement.addEventListener("hide.bs.modal", function (event) {
                document.getElementsByTagName("body").forEach(ele => ele.classList.remove("overflow-hidden"));
            });

            editUserDetailModalElement.addEventListener("show.bs.modal", function (event) {
                $(updateButton).prop("disabled", true);
                document.getElementsByTagName("body").forEach(ele => ele.classList.add("overflow-hidden"));
            })

            const afterSubmit = (response) => {
                if (response) {
                    updateInfo();
                    clearErrors();
                    editUserDetailModal.hide();
                }
            };

            const onSubmitErrors = (swaltitle, errors, resubmitFunction, errorResponse) => {
                clearErrors();
                $.each(errorResponse.response.data.errors || errorResponse.response.data.error, function (attribute, error) {
                    $('#' + attribute).addClass('is-invalid');

                    $.each(error, function (index, value) {
                        if ($('#' + attribute).parent().find('.invalid-feedback').length > 0) {
                            $('#' + attribute).parent().find('.invalid-feedback').html(value);
                        } else {
                            $('#' + attribute).parent().append('<div class="invalid-feedback">' + value + '</div>');
                        }

                    });
                });

                fireSwal("danger",
                    swaltitle,
                    `<ul class="text-danger text-start">` + errors + `</ul>`,
                    true,
                    "{{ __("general.button.ok") }}",
                    null,
                    onConfirm = function() {
                        Swal.close();
                    }
                );
            };

            initFormSubmission(
                $(editUserModalForm),
                "{{ __("layout.spinner.saving") }}",
                "{{ __("user.message.fail_update") }}",
                {
                    submitButton: updateButton,
                    url: function () {
                        return "{{ route("profile.update") }}";
                    },
                    useFormOnSubmit: false,
                    useButtonOnClick: true,
                    skipValidation: true,
                    useToastOnSuccess: true,
                    callbacks: {
                        beforeSubmit: async function () {
                            //
                        },
                        onSubmitErrors: onSubmitErrors,
                        afterSubmit: afterSubmit,
                    }
                }
            );

            // check if any input or select changed in form
            let isFormChanged = false;
            $(editUserModalForm).find("input, select").on("change", function () {
                isFormChanged = true;
                $(updateButton).prop("disabled", false);
            });
        });

        function clearErrors() {
            $(editUserModalForm).find('.invalid-feedback').remove();
            $(editUserModalForm).find('.is-invalid').removeClass('is-invalid');
        }

        function updateInfo() {
            // axios get
            axios.get("{{ route("user.show", ["user" => $profile->id]).'?profile_only' }}")
                .then(function (response) {
                    const profile = response.data;

                    $('#status_card_name').text(profile.name);
                    $('#displayEmail').text(profile.email);
                    if (profile.mobile != null && profile.mobile != '') {
                        $('#displayMobile').text(profile.mobile);
                        $('#displayMobile').parent().removeClass('d-none');
                    } else {
                        $('#displayMobile').parent().addClass('d-none');
                    }
                    $('#displayTimezone').html(profile.country_timezone);

                    // loop profile.roles
                    var role = '';
                    $.each(profile.roles, function (index, value) {
                        role += '<span>' + value.name + '</span>';
                        if (index < profile.roles.length - 1) {
                            role += '<span class="mx-3">|</span>';
                        }
                    });
                    $('#roles').html(role);
                })
                .catch(function (error) {
                    toastr.error(error.response?.data?.message || (error.response?.data?.errors !== undefined ? Object.values(error.response?.data?.errors)?.[0]?.[0] : (error.response?.data?.error || "{{ __("general.message.please_try_again") }}")));
                });
        }
    </script>
@endpush
