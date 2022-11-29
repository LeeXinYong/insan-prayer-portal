<x-base-layout>
    <x-slot name="page_title_slot">{{ __("failed_job_log.page_title.index") }}</x-slot>

    <!--begin::Card-->
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <div class="d-flex align-items-center position-relative me-3 my-1">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 text-dark position-absolute ms-6") !!}
                    <input type="text" name="dtSearch" id="dtSearch" class="form-control w-250px ps-15" placeholder="{{ __("general.message.search") }}">
                </div>
                <div class="d-flex align-items-center position-relative me-3 my-1" id="dateTimeRangePicker">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-1 position-absolute ms-6") !!}
                    <input type="text" name="dateTimeRange" id="dateTimeRange" class="form-control w-250px ps-15" placeholder="{{ __("general.message.filter_date") }}">
                    <input type="hidden" name="startDate" id="startDate" value="">
                    <input type="hidden" name="endDate" id="endDate" value="">
                </div>
            </div>
            <!--end::Card title-->

            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                @canany(["retry", "delete"], "FailedJobLog")
                    @include("pages.common-components.buttons.hover-buttons.hover-button", [
                        "color" => "btn-custom-secondary btn-active-custom-light",
                        "size" => "btn-sm btn-icon",
                        "attributes" => "data-kt-menu-trigger=click data-kt-menu-placement=bottom-end",
                        "icon" => "fs-3 la la-ellipsis-h",
                        "icon_only" => true
                    ])
                @else
                @endcanany

                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px py-3" data-kt-menu="true">
                    <!--begin::Heading-->
                    <div class="menu-item px-3">
                        <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">{{ __("general.button.action") }}</div>
                    </div>
                    <!--end::Heading-->
                    @can("retry", "FailedJobLog")
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        @include("pages.common-components.buttons.hover-buttons.hover-link-button", [
                            "id" => "retryAll",
                            "size" => "btn-sm w-100",
                            "classes" => "text-start",
                            "attributes" => "data-retry=" . route("system.log.failed_job.retry", "all"),
                            "label" => __("failed_job_log.button.retry_all"),
                            "icon" => null
                        ])
                    </div>
                    <!--end::Menu item-->
                    @endcan

                    @can("delete", "FailedJobLog")
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        @include("pages.common-components.buttons.hover-buttons.hover-link-button", [
                            "id" => "clearAll",
                            "size" => "btn-sm w-100",
                            "classes" => "text-start text-danger",
                            "attributes" => "data-destroy-all=" . route("system.log.failed_job.destroy", "all"),
                            "label" => __("failed_job_log.button.clear_all"),
                            "icon" => null
                        ])
                    </div>
                    <!--end::Menu item-->
                    @endcan
                </div>
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-6">
            @include("pages.common-components._table")

            @include("pages.common-components._empty-state-table", [
                "table" => $dataTable->getTableId(),
                "img" => "/demo3/customize/media/empty-states/activity.svg",
                "message" => __("empty_states.failed_job_log.content"),
            ])
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->


    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function () {
                const table = window.{{ config("datatables-html.namespace", "LaravelDataTables") }}["{{ $dataTable->getTableId() }}"];

                $("#dtSearch").keyup(function () {
                    table.search($(this).val()).draw();
                });

                $("#dateTimeRangePicker").daterangepicker({
                    buttonClasses: "btn",
                    applyClass: "btn-custom-gradient btn-active-custom-gradient rounded-1",
                    cancelClass: "btn-custom-light btn-active-custom-light rounded-1 btn-outline",
                    opens: "right",
                    timePicker: 0,
                    timePickerIncrement: 5,
                    locale: {
                        cancelLabel: "{{ __("general.message.clear") }}",
                        format: "MM/DD/YYYY"
                    }
                }).on("apply.daterangepicker", function(ev, picker) {
                    $("#dateTimeRangePicker .form-control").val(picker.startDate.format("DD/MM/YYYY") + " to " + picker.endDate.format("DD/MM/YYYY"));
                    $("#startDate").val(picker.startDate.format("YYYY-MM-DD"));
                    $("#endDate").val(picker.endDate.format("YYYY-MM-DD"));

                    // Event listener to the two range filtering inputs to redraw on input
                    table.draw();
                }).on("cancel.daterangepicker", function(ev, picker) {
                    $("#dateTimeRangePicker .form-control").val("");
                    $("#startDate").val("");
                    $("#endDate").val("");

                    $("#dateTimeRangePicker").data("daterangepicker").setStartDate(moment());
                    $("#dateTimeRangePicker").data("daterangepicker").setEndDate(moment());

                    // Event listener to the two range filtering inputs to redraw on input
                    table.draw();
                });

                $(document).on("click", "[data-retry]", function (e) {
                    e.preventDefault();
                    const retryElement = $(this);

                    Swal.fire({
                        text: ($(this).attr("id") === "retryAll") ? "{{ __("failed_job_log.message.are_you_sure_to_retry_all_job") }}" : "{{ __("failed_job_log.message.are_you_sure_to_retry_job") }}",
                        showCancelButton: true,
                        reverseButtons: true,
                        buttonsStyling: false,
                        confirmButtonText: "{{ __("general.message.yes") }}",
                        cancelButtonText: "{{ __("general.button.cancel") }}",
                        customClass: {
                            popup: "swal2-warning",
                            confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                            cancelButton: "btn btn-custom-light btn-active-custom-light min-w-60px min-w-lg-150px min-h-40px rounded-1 btn-outline"
                        }
                    }).then(function (result) {
                        if (result.value) {
                            axios.post(retryElement.data("retry"), {
                                "_method": "POST",
                            })
                            .then(function (response) {
                                toastr.success(response.data?.message || "{{ __("general.message.success") }}");
                                table.ajax.reload();
                            })
                            .catch(function (error) {
                                toastr.error(error.response?.data?.error || "{{ __("general.message.please_try_again") }}");
                                console.log(error);
                            });
                        }
                    });
                });

                $(document).on("click", "[data-destroy-all]", function (e) {
                    e.preventDefault();
                    const deleteElement = $(this);
                    Swal.fire({
                        title: "{{ __("general.message.confirmation") }}",
                        text: "{{ __("general.message.delete_msg") }}",
                        showCancelButton: true,
                        reverseButtons: true,
                        buttonsStyling: false,
                        confirmButtonText: "{{ __("general.message.yes") }}",
                        cancelButtonText: "{{ __("general.button.cancel") }}",
                        customClass: {
                            popup: "swal2-danger",
                            confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                            cancelButton: "btn btn-custom-light btn-active-custom-light min-w-60px min-w-lg-150px min-h-40px rounded-1 btn-outline"
                        }
                    }).then(function (result) {
                        if (result.value) {
                            axios.delete(deleteElement.data("destroy-all"), {
                                "_method": "DELETE",
                            })
                            .then(function (response) {
                                toastr.success(response.data?.message || "{{ __("general.message.deleted") }}");
                                table.ajax.reload();
                            })
                            .catch(function (error) {
                                toastr.error(error.response?.data?.error || "{{ __("general.message.please_try_again") }}");
                                console.log(error);
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
</x-base-layout>
