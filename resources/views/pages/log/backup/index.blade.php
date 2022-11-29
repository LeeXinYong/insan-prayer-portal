<x-base-layout>
    <x-slot name="page_title_slot">{{ __("backup_log.page_title.index") }}</x-slot>

    <div class="card card-flush mb-10" id="backupDestinationsCard">
        <!--begin::Body-->
        <div class="card-body pt-2">
            <!--begin::Nav-->
            <ul class="nav nav-pills nav-pills-custom mb-3">
                <!--begin::Item-->
                <li class="nav-item mb-3 me-3 me-lg-6">
                    <!--begin::Link-->
                    <a class="nav-link d-flex justify-content-between flex-column flex-center overflow-hidden py-4 active" data-bs-toggle="pill" href="#backupDestinationTabContent">
                        <!--begin::Subtitle-->
                        <span class="nav-text text-gray-700 fw-bolder fs-6 lh-1">
                            {!! theme()->getSvgIcon("icons/duotune/files/fil023.svg", "svg-icon-3x svg-icon-primary me-2") !!}
                            {{ __('backup_log.destination_status.tabs.backup_destinations') }}
                        </span>
                        <!--end::Subtitle-->
                        <!--begin::Bullet-->
                        <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                        <!--end::Bullet-->
                    </a>
                    <!--end::Link-->
                </li>
                <!--end::Item-->
                <!--begin::Item-->
                <li class="nav-item mb-3 me-3 me-lg-6">
                    <!--begin::Link-->
                    <a class="nav-link d-flex justify-content-between flex-column flex-center overflow-hidden py-4" data-bs-toggle="pill" href="#unhealthyDestinationTabContent">
                        <!--begin::Subtitle-->
                        <span class="nav-text text-gray-700 fw-bolder fs-6 lh-1">
                            {!! theme()->getSvgIcon("icons/duotune/general/gen050.svg", "svg-icon-3x svg-icon-danger me-2") !!}
                            {{ __('backup_log.destination_status.tabs.unhealthy_destinations') }}
                        </span>
                        <!--end::Subtitle-->
                        <!--begin::Bullet-->
                        <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-danger"></span>
                        <!--end::Bullet-->
                    </a>
                    <!--end::Link-->
                </li>
                <!--end::Item-->
            </ul>
            <!--end::Nav-->
            <!--begin::Tab Content-->
            <div class="tab-content">
                <!--begin::Tap pane-->
                <div class="tab-pane fade active show" id="backupDestinationTabContent">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-row-dashed align-middle gs-0 gy-4 my-0" id="backupDestinationTable">
                            <!--begin::Table head-->
                            <thead>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                            </tbody>
                            <!--end::Table body-->
                        </table>
                    </div>
                    <!--end::Table-->

                    <div class="overlay overlay-block loadingSpinner">
                        <div class="overlay-wrapper p-5">
                            <div class="dataTables_wrapper"><div class="dataTables_processing" style="display: block;"><div><div></div><div></div><div></div><div></div></div></div></div>
                        </div>
                    </div>
                </div>
                <!--end::Tap pane-->
                <!--begin::Tap pane-->
                <div class="tab-pane fade" id="unhealthyDestinationTabContent">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-row-dashed align-middle gs-0 gy-4 my-0" id="unhealthyDestinationTable">
                            <!--begin::Table head-->
                            <thead>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                            </tbody>
                            <!--end::Table body-->
                        </table>
                    </div>
                    <!--end::Table-->

                    <div class="overlay overlay-block loadingSpinner">
                        <div class="overlay-wrapper p-5">
                            <div class="dataTables_wrapper"><div class="dataTables_processing" style="display: block;"><div><div></div><div></div><div></div><div></div></div></div></div>
                        </div>
                    </div>
                </div>
                <!--end::Tap pane-->
            </div>
            <!--end::Tab Content-->
        </div>
        <!--end: Card Body-->
    </div>

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
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-6">
            @include("pages.common-components._table")
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->


    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function () {
                getBackupStatusTable('#backupDestination', '#unhealthyDestination');

                function getBackupStatusTable(selector1, selector2) {
                    var tab1 = selector1+'TabContent';
                    var table1 = selector1+'Table';
                    var tab2 = selector2+'TabContent';
                    var table2 = selector2+'Table';

                    // Send ajax request
                    axios.get("{{ route('system.log.backup.destinationstatus') }}")
                        .then(function (response) {
                            if(response.data.success) {
                                toastr.success(response.data.success);
                            }

                            initBackupStatusTable(table1, response.data.destinationList);
                            initBackupStatusTable(table2, response.data.unhealthyDestinationList);
                        })
                        .catch(function (error) {
                            toastr.error(error.response?.data?.message || (error.response?.data?.errors !== undefined ? Object.values(error.response?.data?.errors)?.[0]?.[0] : (error.response?.data?.error || "{{ __("general.message.please_try_again") }}")));
                        })
                        .then(function () {
                            // Hide spinner
                            $(tab1).find('.loadingSpinner').fadeOut();
                            $(tab2).find('.loadingSpinner').fadeOut();

                            if($(table1).find('tbody tr').length === 0) {
                                $(table1).find('tbody').append('<tr><td colspan="4" class="text-center">{{ __('backup_log.message.no_backup_destinations_found') }}</td></tr>');
                            }

                            if($(table2).find('tbody tr').length === 0) {
                                $(table2).find('tbody').append('<tr><td colspan="4" class="text-center">{{ __('backup_log.message.no_unhealthy_destinations_found') }}</td></tr>');
                            }
                        });
                }

                function initBackupStatusTable(table, data) {
                    if(Object.keys(data).length > 0) {
                        if(Object.keys(data.thead).length > 0) {
                            $(table+' thead').empty('');

                            var header = '';
                            $.each(data.thead, function (index, value) {
                                header += '<th>'+value+'</th>';
                            });
                            $(table+ ' thead').append("<tr class='fs-7 fw-bolder text-gray-500 border-bottom-0'>"+header+"</tr>");
                        }

                        if(Object.keys(data.tbody).length > 0) {
                            $(table+' tbody').empty('');

                            $.each(data.tbody, function (index, value) {
                                var row = '';
                                $.each(value, function (index, value) {
                                    row += '<td>'+value+'</td>';
                                });
                                $(table+' tbody').append('<tr>'+row+'</tr>');
                            });
                        }
                    }
                }

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
                }).on('cancel.daterangepicker', function(ev, picker) {
                    $("#dateTimeRangePicker .form-control").val("");
                    $('#startDate').val("");
                    $('#endDate').val("");

                    $('#dateTimeRangePicker').data('daterangepicker').setStartDate(moment());
                    $('#dateTimeRangePicker').data('daterangepicker').setEndDate(moment());

                    // Event listener to the two range filtering inputs to redraw on input
                    table.draw();
                });
            });
        </script>
    @endpush
</x-base-layout>
