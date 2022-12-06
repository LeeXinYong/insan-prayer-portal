{{-- <x-base-layout> --}}
    <?php 
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']; 
    ?>
<?php
$ipaddress = $_SERVER['REMOTE_ADDR'];
echo "Your IP Address is " . $ipaddress;
?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <x-slot name="page_title_slot">{{ __("prayer_time.page_title.index") }}</x-slot>

    {{-- <div class="p-2">    
        <ol class="breadcrumb text-muted fs-6 fw-semibold">
            <li class="breadcrumb-item pe-3"><a href="{{ theme()->getPageUrl('') }}" class="pe-3">Dashboard</a></li>
            <li class="breadcrumb-item pe-3 text-muted">Timeslot</li>
        </ol>
    </div> --}}

    <!--begin::Card-->
    <div class="card card-flush">
        <!--begin::Header-->
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Timeslot</span>
                <span class="text-muted fw-bold fs-7">Today</span>
            </h3>
    
            <div class="card-toolbar">
                <!--begin::Menu-->
                <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen024.svg", "svg-icon-2");  !!}
                </button>
                
                <!--begin::Menu 3-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px py-3" data-kt-menu="true">
                    <!--begin::Heading-->
                    <div class="menu-item px-3">
                        <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                            CONFIGURATION
                        </div>
                    </div>
                    <!--end::Heading-->

                    <!--begin::Menu item-->
                    <div class="menu-item px-3" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-trigger="hover" title="Coming soon">
                        <a href="{{ route('prayer_time.index') }}" class="menu-link px-3">
                            TIMESLOT
                        </a>
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu item-->
                    <div class="menu-item px-3" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-trigger="hover" title="Coming soon">
                        <a href="{{ route('zone.index') }}" class="menu-link px-3">
                            ZONE
                        </a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu 3-->


                <!--end::Menu-->
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Card body-->
        <div class="card-body pt-6">

            <table class="table" id="prayer_time_datatable">
                <thead>
                    <th>Zone Code</th>
                    <th>Zone</th>
                    <th>Date</th>
                    <th>IMSAK</th>
                    <th>FAJR</th>
                    <th>SYURUK</th>
                    <th>DHUHR</th>
                    <th>ASR</th>
                    <th>MAGHRIB</th>
                    <th>ISHA</th>
                </thead>
            </table>

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
                var table = $('#prayer_time_datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{!! route('prayer_time.dashboard') !!}",
                        data: function(d) {
                            d.hidden_input = $('#hidden_input').val()
                        }
                    },
                    columns: [
                        {data: 'zone_id', name: 'zone_id'},
                        {data: 'name', name: 'name'},
                        {data: 'gregorian_date', name: 'gregorian_date'},
                        {data: 'imsak', name: 'imsak'},
                        {data: 'fajr', name: 'fajr'},
                        {data: 'syuruk', name: 'syuruk'},
                        {data: 'dhuhr', name: 'dhuhr'},
                        {data: 'asr', name: 'asr'},
                        {data: 'maghrib', name: 'maghrib'},
                        {data: 'isha', name: 'isha'},
                    ],
                    bFilter: false
                });
            });
        </script>
    @endpush
{{-- </x-base-layout> --}}
