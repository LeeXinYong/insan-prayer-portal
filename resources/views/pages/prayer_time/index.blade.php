<x-base-layout>
    <?php 
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']; 
    ?>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <x-slot name="page_title_slot">{{ __("prayer_time.page_title.index") }}</x-slot>

    <div class="p-2">    
        <ol class="breadcrumb text-muted fs-6 fw-semibold">
            <li class="breadcrumb-item pe-3"><a href="{{ theme()->getPageUrl('') }}" class="pe-3">Dashboard</a></li>
            <li class="breadcrumb-item pe-3 text-muted">Timeslot</li>
        </ol>
    </div>

    <!--begin::Card-->
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <div class="row mt-10">
                    <div class="d-flex align-items-center position-relative my-1 col-md-6">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 text-dark position-absolute ms-6") !!}
                        <input type="text" name="dtSearch" id="dtSearch" class="form-control w-md-250px ps-15 mt-6 mb-6" placeholder="Search Zone..." />
                        <input type="text" id="hidden_input" hidden />
                    </div>
                    <div class="col-md-6 row">
                        <div class="col-12 row">
                            <div class="card border-primary mb-3">
                                <div class="row g-0">
                                    <div class="col-2 position-relative">
                                        <div class="form-check form-check-custom form-check-solid position-absolute top-50 start-50 translate-middle">
                                            <input class="form-check-input radio-date" id="radio_d" type="radio" name="data_filter" checked="checked"/>
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <div class="form-floating">
                                            <input type="text" name="date" id="date" class="form-control search_date" placeholder="" value="{{ date('d-M-Y') }}" required/>
                                            <label class="form-label" for="date">Date</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 row">
                            <div class="card mb-3">
                                <div class="row g-0">
                                    <div class="col-2 position-relative">
                                        <div class="form-check form-check-custom form-check-solid position-absolute top-50 start-50 translate-middle">
                                            <input class="form-check-input radio-date" id="radio_m" type="radio" name="data_filter"/>
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <div class="form-floating">
                                            <select name="month" id="month" class="form-select" required disabled>
                                                <option value="">{{ __("general.message.please_select") }}</option>
                                                @foreach($months as $month)
                                                    <option value="{{ $month }}" {{ $month == date('M') ? "selected" : "" }}>{{ $month }}</option>
                                                @endforeach
                                            </select>
                                            <label class="form-label" for="month">Month</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar gap-3">
                @can("arrange", \App\Models\PrayerTime::class)
                    @include("pages.common-components.buttons.rearrange-items-buttons")
                @endcan

                @can("create", \App\Models\PrayerTime::class)
                    @include("pages.common-components.buttons.add-button", [
                        "link" => route("prayer_time.create")
                    ])
                @endcan
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-6">

            @include("pages.common-components._table")

            @include("pages.common-components._empty-state-table", [
                "table" => $dataTable->getTableId(),
                "img" => "/demo3/customize/media/empty-states/document.svg",
                "message" => __("empty_states.default.content"),
                "button_label" => __("empty_states.default.action"),
                "url" => Auth::user()->cannotCreate(\App\Models\PrayerTimes::class) ? null : route("prayer_time.create")
            ])
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->

    @include("pages.common-components._mobile-preview-modal")

    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/plug-ins/1.13.1/api/row().show().js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                const table = window.{{ config("datatables-html.namespace", "LaravelDataTables") }}["{{ $dataTable->getTableId() }}"]; 

                $("#dtSearch").keyup(function () {
                    searchTimeslot();
                });

                function radio_change_events() {
                    if (!$("#radio_d").is(":checked")) {
                        $("#month").prop("disabled", false);
                        $('#date').prop("disabled", true);
                    }
                    else if (!$("#radio_m").is(":checked")) {
                        $("#month").prop("disabled", true);
                        $('#date').prop("disabled", false);
                    }
                }

                function readSearchStrings () {
                    var zone = $('#dtSearch').val();
                    var month_year = ($('#month').val() != '') ? $('#month').val() + '-2022' : '';
                    var dt = ($("#radio_d").is(":checked")) ? $('#date').val() : month_year;
                    var final_string = zone + ' ' + dt;
                    $('#hidden_input').val(final_string);
                }

                function searchTimeslot () {
                    readSearchStrings();
                    // var new_row = {
                    //     zone_id: 'WLY01'
                    // };
                    
                    // table.row.add( new_row ).draw().show().draw(false);
                    table.search($('#hidden_input').val()).draw();
                }

                $(".search_date#date").flatpickr({
                    dateFormat: "d-M-Y",
                    onChange: function(selectedDates, dateStr, instance) {
                        searchTimeslot();
                    },
                });

                $("#month").select2({
                    placeholder: "{{ __("general.message.please_select") }}",
                    allowClear: false,
                    width: '100%',
                });

                $("#month").on('change', function () {
                    searchTimeslot();
                });

                $('.radio-date').on('click', function () {
                    radio_change_events();
                    searchTimeslot();
                })

                searchTimeslot();

                radio_change_events();
            });
        </script>
    @endpush
</x-base-layout>
