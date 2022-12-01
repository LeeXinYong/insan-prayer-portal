<x-base-layout>
    <x-slot name="page_title_slot">{{ __("prayer_time.page_title.edit") }}</x-slot>
    <!--begin::Form-->
    <form method="post" id="edit_prayer_time_form" action="{{ route("prayer_time.update", ["prayer_time" => $prayer_time->prayer_id]) }}" enctype="multipart/form-data">
        @csrf
        @method("PUT")
        <!--begin::Card-->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="row mt-10">
                        <div class="col-12">
                            <span class="fw-bolder mb-2 text-dark">{{ $prayer_time->zone->zone_id }}</span>
                            <span class="text-muted fw-bold fs-7">{{ $prayer_time->zone->name }}</span>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-12">
                            <label class="form-label" for="imsak">IMSAK</label>
                            <input type="text" name="imsak" id="imsak" class="form-control" value="{{ $prayer_time->imsak }}" required/>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="fajr">FAJR</label>
                            <input type="text" name="fajr" id="fajr" class="form-control" value="{{ $prayer_time->fajr }}" required/>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="syuruk">SYURUK</label>
                            <input type="text" name="syuruk" id="syuruk" class="form-control" value="{{ $prayer_time->syuruk }}" required/>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="dhuhr">DHUHR</label>
                            <input type="text" name="dhuhr" id="dhuhr" class="form-control" value="{{ $prayer_time->dhuhr }}" required/>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="asr">ASR</label>
                            <input type="text" name="asr" id="asr" class="form-control" value="{{ $prayer_time->asr }}" required/>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="maghrib">MAGHRIB</label>
                            <input type="text" name="maghrib" id="maghrib" class="form-control" value="{{ $prayer_time->maghrib }}" required/>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="isha">ISHA</label>
                            <input type="text" name="isha" id="isha" class="form-control" value="{{ $prayer_time->isha }}" required/>
                        </div>
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
        <script type="text/javascript">
            $(document).ready(function() {

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

