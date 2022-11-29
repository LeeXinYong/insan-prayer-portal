<x-base-layout>
    <x-slot name="page_title_slot">{{ __("audit_log.page_title.index") }}</x-slot>
    <!--begin::Card-->
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <div class="d-flex flex-column flex-md-row flex-lg-column flex-xl-row align-items-center justify-content-start my-1">
                    <div class="d-flex align-items-center position-relative me-3 my-1">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                        <input type="text" name="dtSearch" id="dtSearch" class="form-control w-250px ps-15" placeholder="{{ __("general.message.search") }}">
                    </div>
                    <div class="d-flex align-items-center position-relative me-3 my-1" id="dateTimeRangePicker">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-1 position-absolute ms-6") !!}
                        <input type="text" name="dateTimeRange" id="dateTimeRange" class="form-control w-250px ps-15" placeholder="{{ __("general.message.filter_date") }}">
                        <input type="hidden" name="startDate" id="startDate" value="">
                        <input type="hidden" name="endDate" id="endDate" value="">
                    </div>
                    <div class="d-flex align-items-center position-relative me-3 my-1">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen031.svg", "svg-icon-1 position-absolute ms-6 z-index-3") !!}
                        <select class="form-select w-250px ps-15" id="filterModule">
                            <option value="">{{ __("general.message.please_select") }}</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module[0] }}">{{ $module[1] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <!--end::Card title-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-6">
            @include('pages.log.audit._table')
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
                }).on('cancel.daterangepicker', function(ev, picker) {
                    $("#dateTimeRangePicker .form-control").val("");
                    $('#startDate').val("");
                    $('#endDate').val("");

                    $('#dateTimeRangePicker').data('daterangepicker').setStartDate(moment());
                    $('#dateTimeRangePicker').data('daterangepicker').setEndDate(moment());

                    // Event listener to the two range filtering inputs to redraw on input
                    table.draw();
                });

                $("#filterModule").select2({
                    placeholder: "{{ __("general.message.filter_by_module") }}",
                    allowClear: true,
                    width: "100%"
                }).change(function () {
                    table.draw();
                });
            });
        </script>
    @endpush
</x-base-layout>
