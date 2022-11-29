<x-base-layout>
    <x-slot name="page_title_slot">{{ __("changelog.page_title.create") }}</x-slot>
    <!--begin::Form-->
    <form method="post" id="create_changelog_form" action="{{ route("system.log.changelog.store") }}" class="form">
        @csrf
        <!--begin::Card-->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label for="version" class="form-label">{{ __("changelog.form_label.version") }} <span data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" data-bs-template="<div class='tooltip' role='tooltip'><div class='tooltip-arrow'></div><div class='tooltip-inner text-start'></div></div>" title="{!! __("changelog.message.version_format_msg") !!}"><i class="bi bi-question-circle-fill text-muted"></i></span></label>
                        <input type="text" name="version" id="version" class="form-control form-control-solid" pattern="^[0-9.]+$" />
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-6">
                        <label for="released_by" class="form-label">{{ __("changelog.form_label.released_by") }}</label>
                        <input type="text" name="released_by" id="released_by" class="form-control form-control-solid" />
                    </div>
                    <div class="col-6">
                        <label for="released_at" class="form-label">{{ __("changelog.form_label.released_at") }}</label>
                        <input type="text" name="released_at" id="released_at" class="form-control form-control-solid" placeholder="Select date" />
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-12 table-responsive">
                        <!--begin: Datatable -->
                        <table id="description_list" class="table table-row-bordered">
                            <thead>
                            <tr class="fw-bold fs-6 text-gray-800 align-middle">
                                <th class="form-label" style="width:95%">{{ __("changelog.form_label.description") }}</th>
                                <th class="form-label text-end" style="width:5%">
                                    @include("pages.common-components.buttons.add-button", [
                                        "id" => "add_description_btn",
                                        "size" => "btn-icon",
                                        "icon_only" => true
                                    ])
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="pr-0">
                                    <input type="text" name="description[]" class="form-control form-control-solid" />
                                </td>
                                <td class="pr-0 text-end">
                                    @include("pages.common-components.buttons.delete-button", [
                                        "size" => "btn-icon",
                                        "classes" => "remove-description-btn",
                                        "icon_only" => true
                                    ])
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    @include("pages.common-components.buttons.cancel-button", [
                        "classes" => "btn-outline"
                    ])
                    @include("pages.common-components.buttons.create-button", [
                        "indicator" => true
                    ])
                </div>
            </div>
        </div>
        <!--end::Card-->
    </form>
    <!--end::Form-->

    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function() {
                // Initialize datepicker
                $("#released_at").daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    locale: {
                        format: "D MMM yyyy"
                    }
                });

                // Add Description Row
                $("#add_description_btn").on("click", function() {
                    // close last row, append it to the list, empty the input and focus it
                    $("#description_list tbody").append($("#description_list tbody tr:last").clone(true, true)).find("input:last").val("").filter(":visible:first").focus();
                });

                // Remove Description Row
                $(".remove-description-btn").on("click", function() {
                    if ($("#description_list tbody tr").length > 1) {
                        $(this).closest("tr").remove();
                    } else {
                        toastr.error("{{ __("changelog.message.remove_description_error_msg") }}");
                    }
                });

                initFormSubmission(
                    $("#create_changelog_form"),
                    "{{ __("layout.spinner.creating") }}",
                    "{{ __("changelog.message.fail_create") }}",
                    {
                        callbacks: {
                            beforeSubmit: async function () {
                                if (!$("input:invalid").length && !$("select:invalid").length) {
                                    let result = await swal.fire({
                                        title: "{{ __("changelog.message.confirmation_prompt") }}",
                                        html: "{{ __("changelog.message.confirmation_prompt_msg") }}",
                                        showCancelButton: true,
                                        reverseButtons: true,
                                        buttonsStyling: false,
                                        confirmButtonText: "{{ __("general.button.confirm") }}",
                                        cancelButtonText: "{{ __("general.button.cancel") }}",
                                        customClass: {
                                            popup: "swal2-warning",
                                            confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                                            cancelButton: "btn btn-custom-light btn-active-custom-light min-w-60px min-w-lg-150px min-h-40px rounded-1 btn-outline"
                                        }
                                    });

                                    return result.isConfirmed;
                                }
                            }
                        }
                    }
                );
            });
        </script>
    @endpush
</x-base-layout>
