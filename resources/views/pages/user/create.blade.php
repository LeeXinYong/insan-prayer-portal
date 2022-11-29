<x-base-layout>
    <x-slot name="page_title_slot">{{ __("user.page_title.create") }}</x-slot>
    <!--begin::Form-->
    <form method="post" id="create_user_form" action="{{ route("user.store") }}" enctype="multipart/form-data">
        @csrf
        <!--begin::Card-->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <label class="form-label" for="name">{{ __("user.form_label.name") }}</label>
                        <input type="text" name="name" id="name" class="form-control" required/>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-6">
                        <label class="form-label" for="email">{{ __("user.form_label.email") }}</label>
                        <input type="email" name="email" id="email" class="form-control" required/>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="mobile">{{ __("user.form_label.mobile") }} <span class="fs-9 fst-italic">({{ __("general.form_label.optional") }})</span></label>
                        <input type="text" name="mobile" id="mobile" class="form-control"/>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-6">
                        <label class="form-label">{{ __("user.form_label.status") }}</label>
                        <!--begin::Switch-->
                        <label class="form-check form-switch form-check-custom form-check-solid px-switch">
                            <!--begin::Input-->
                            <input type="checkbox" name="status" id="status" class="form-check-input" value="1" checked>
                            <!--end::Input-->
                            <!--begin::Label-->
                            <span id="status_text" class="form-check-label fw-bold text-muted">{{ __("general.message.active") }}</span>
                            <!--end::Label-->
                        </label>
                        <!--end::Switch-->
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-6">
                        <label class="form-label" for="country">{{ __("user.form_label.country") }}</label>
                        <select name="country" id="country" class="form-select" required>
                            <option value="">{{ __("general.message.please_select") }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->iso2 }}" data-id="{{ $country->id }}">{{ $country->name }}</option>
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

    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function() {
                $("#country, #timezone").select2({
                    placeholder: "{{ __("general.message.please_select") }}",
                    allowClear: false,
                    width: '100%',
                });

                $('#country').change(function () {
                    getCountryTimezone(
                        "{{ route("getCountryTimezone", ["country_id" => ":id"]) }}".replace(":id", $(this).children("option:selected").data("id")),
                        "{{ __("layout.spinner.retrieving") }}"
                    );
                });

                initSwitchLabel(
                    "#status",
                    "{{ __("general.message.active") }}",
                    "{{ __("general.message.inactive") }}"
                );

                initFormSubmission(
                    $("#create_user_form"),
                    "{{ __("layout.spinner.creating") }}",
                    "{{ __("user.message.fail_create") }}"
                )
            });
        </script>
    @endpush
</x-base-layout>
