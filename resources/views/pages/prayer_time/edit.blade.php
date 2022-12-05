<x-base-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    {{-- <div class="p-2">    
        <ol class="breadcrumb text-muted fs-6 fw-semibold">
            <li class="breadcrumb-item pe-3"><a href="{{ theme()->getPageUrl('') }}" class="pe-3">Dashboard</a></li>
            <li class="breadcrumb-item pe-3"><a href="javascript:history.back()" class="pe-3">Timeslot</a></li>
            <li class="breadcrumb-item pe-3 text-muted">Edit</li>
        </ol>
    </div> --}}
    <x-slot name="page_title_slot">{{ __("prayer_time.page_title.edit") }}</x-slot>
    <!--begin::Form-->
    <form method="post" id="edit_prayer_time_form" action="{{ route("prayer_time.update", ["prayer_time" => $prayer_time->prayer_id]) }}" enctype="multipart/form-data">
        @csrf
        @method("PUT")
        <!--begin::Card-->
        <div class="card">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">{{ $prayer_time->zone->zone_id }}</span>
                    <span class="text-muted fw-bold fs-7">{{ $prayer_time->zone->name }}</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="row mt-10">
                    <div class="col-xxl-4">
                        <label class="form-label" for="imsak">IMSAK</label>
                        <input class="form-control time" name="imsak" id="imsak" placeholder="{{ $prayer_time->imsak }}" value="{{ $prayer_time->imsak }}" required/>
                    </div>
                    <div class="col-xxl-4">
                        <label class="form-label" for="fajr">FAJR</label>
                        <input class="form-control time" name="fajr" id="fajr" placeholder="{{ $prayer_time->fajr }}" value="{{ $prayer_time->fajr }}" required/>
                    </div>
                    <div class="col-xxl-4">
                        <label class="form-label" for="syuruk">SYURUK</label>
                        <input class="form-control time" name="syuruk" id="syuruk" placeholder="{{ $prayer_time->syuruk }}" value="{{ $prayer_time->syuruk }}" required/>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-xxl-4">
                        <label class="form-label" for="dhuhr">DHUHR</label>
                        <input class="form-control time" name="dhuhr" id="dhuhr" placeholder="{{ $prayer_time->dhuhr }}" value="{{ $prayer_time->dhuhr }}" required/>
                    </div>
                    <div class="col-xxl-4">
                        <label class="form-label" for="asr">ASR</label>
                        <input class="form-control time" name="asr" id="asr" placeholder="{{ $prayer_time->asr }}" value="{{ $prayer_time->asr }}" required/>
                    </div>
                    <div class="col-xxl-4">
                        <label class="form-label" for="maghrib">MAGHRIB</label>
                        <input class="form-control time" name="maghrib" id="maghrib" placeholder="{{ $prayer_time->maghrib }}" value="{{ $prayer_time->maghrib }}" required/>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-xxl-4 mx-auto">
                        <label class="form-label" for="isha">ISHA</label>
                        <input class="form-control time" name="isha" id="isha" placeholder="{{ $prayer_time->isha }}" value="{{ $prayer_time->isha }}" required/>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex align-items-center @can("delete", \App\Models\PrayerTime::class) justify-content-between @else justify-content-end @endcan gap-3">
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
        </div>
        <!--end::Card-->
    </form>
    <!--end::Form-->

    {{-- Inject Scripts --}}
    @push("scripts")

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <script type="text/javascript">
            $(document).ready(function() {

                $('.time').each(function(i) {
                    var time = $(this).attr('placeholder');

                    $(this).flatpickr({
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "H:i:00",
                        defaultDate: time
                    });
                });

                initFormSubmission(
                    $("#edit_prayer_time_form"),
                    "{{ __("layout.spinner.saving") }}",
                    "{{ __("prayer_time.message.fail_update") }}"
                );

                $("#url_content_switch").click(function () {
                    if ($(this).prop("checked")) {
                        $("#content_section").hide();
                        $("#url_section").show();
                        $("#url").prop("required", true);
                    } else {
                        $("#url_section").hide();
                        $("#url").prop("required", false);
                        $("#content_section").show();
                    }
                });
            });
        </script>
    @endpush
</x-base-layout>

