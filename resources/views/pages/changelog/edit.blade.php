<x-base-layout>
    <x-slot name="page_title_slot">{{ __("changelog.page_title.edit") }}</x-slot>
    <!--begin::Form-->
    <form method="post" id="edit_changelog_form" action="{{ route("system.log.changelog.update", ["changelog" => $changelog->id]) }}" class="form">
        @csrf
        @method('PUT')
        <!--begin::Card-->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label for="version" class="form-label">{{ __("changelog.form_label.version") }} <span data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" data-bs-template="<div class='tooltip' role='tooltip'><div class='tooltip-arrow'></div><div class='tooltip-inner text-start'></div></div>" title="{!! __("changelog.message.version_format_msg") !!}"><i class="bi bi-question-circle-fill text-muted"></i></span></label>
                        <input type="text" name="version" id="version" class="form-control form-control-solid" pattern="^[0-9.]+$" value="{{ $changelog->version }}" disabled/>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-6">
                        <label for="released_by" class="form-label">{{ __("changelog.form_label.released_by") }}</label>
                        <input type="text" name="released_by" id="released_by" class="form-control form-control-solid" value="{{ $changelog->released_by }}" disabled/>
                    </div>
                    <div class="col-6">
                        <label for="released_at" class="form-label">{{ __("changelog.form_label.released_at") }}</label>
                        <input type="text" name="released_at" id="released_at" class="form-control form-control-solid" placeholder="Select date" value="{{ $changelog->released_at }}" disabled/>
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
                            @if(count($changelog->description) > 0)
                                @foreach($changelog->description as $description)
                                    <tr>
                                        <td class="pr-0">
                                            <input type="text" name="description[]" class="form-control form-control-solid" value="{{ $description }}" required/>
                                        </td>
                                        <td class="pr-0 text-end">
                                            @include("pages.common-components.buttons.delete-button", [
                                                "size" => "btn-icon",
                                                "classes" => "remove-description-btn",
                                                "icon_only" => true
                                            ])
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="pr-0">
                                        <input type="text" name="description[]" class="form-control form-control-solid" required/>
                                    </td>
                                    <td class="pr-0 text-end">
                                        @include("pages.common-components.buttons.delete-button", [
                                            "size" => "btn-icon",
                                            "classes" => "remove-description-btn",
                                            "icon_only" => true
                                        ])
                                    </td>
                                </tr>
                            @endif
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
                    @include("pages.common-components.buttons.save-button", [
                        "indicator" => true
                    ])
                </div>
            </div>
        </div>
        <!--end::Card-->
    </form>
    <!--end::Form-->

    @push("scripts")
        <!-- Normal Form JS -->
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
                $('#add_description_btn').on("click", function() {
                    // close last row, append it to the list, empty the input and focus it
                    $("#description_list tbody").append($("#description_list tbody tr:last").clone(true, true)).find("input:last").val("").filter(":visible:first").focus();
                });

                // Remove Description Row
                $('.remove-description-btn').on('click', function() {
                    const rows = $("#description_list tbody tr").length;
                    if (rows > 1) {
                        $(this).closest("tr").remove();
                    } else {
                        toastr.error("{{ __("changelog.message.remove_description_error_msg") }}");
                    }
                });

                initFormSubmission(
                    $("#edit_changelog_form"),
                    "{{ __("layout.spinner.saving") }}",
                    "{{ __("changelog.message.fail_update") }}"
                );
            });
        </script>
    @endpush
</x-base-layout>
