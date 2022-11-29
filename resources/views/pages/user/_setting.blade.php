<!--begin::Form-->
<form method="post" id="edit_user_form" action="{{ route("user.update", ["user" => $user->id]) }}" enctype="multipart/form-data">
@csrf
@method("PUT")
<!--begin::Card-->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <label class="form-label" for="name">{{ __("user.form_label.name") }}</label>
                    <input type="text" name="name" id="name" class="form-control form-control-solid" value="{{ $user->name }}" required/>
                </div>
                <div class="col-6">
                    <label class="form-label" for="email">{{ __("user.form_label.email") }}</label>
                    <input type="email" name="email" id="email" class="form-control form-control-solid" value="{{ $user->email }}" required/>
                </div>
            </div>
            <div class="row mt-10">
                <div class="col-6">
                    <label class="form-label" for="mobile">{{ __("user.form_label.mobile") }} <span class="fs-9 fst-italic">({{ __("general.form_label.optional") }})</span></label>
                    <input type="text" name="mobile" id="mobile" class="form-control form-control-solid" value="{{ $user->mobile }}"/>
                </div>
            </div>
            <div class="row mt-10">
                <div class="col-6">
                    <label class="form-label" for="country">{{ __("user.form_label.country") }}</label>
                    <select name="country" id="country" class="form-select form-select-solid" required>
                        <option value="">{{ __("general.message.please_select") }}</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->iso2 }}" data-id="{{ $country->id }}" {{ $country->iso2 == $user->country_code ? "selected" : "" }}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label" for="timezone">{{ __("user.form_label.timezone") }}</label>
                    <select name="timezone" id="timezone" class="form-select form-select-solid" required>
                        <option value="">{{ __("general.message.please_select") }}</option>
                    </select>
                    <div class="form-text text-muted">{{ __("user.message.timezone_msg") }}</div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex align-items-center justify-content-end">
                <button type="submit" class="btn btn-success">
                    @include("partials.general._button-indicator", ["label_icon" => "la la-save", "label" => __("general.button.save"), "message" => __("general.button.saving")])
                </button>
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

            $("#country").change(function () {
                getCountryTimezone(
                    "{{ route("getCountryTimezone", ["country_id" => ":id"]) }}".replace(":id", $(this).children("option:selected").data("id")),
                    "{{ __("layout.spinner.retrieving") }}",
                    "{{ $user->timezone }}"
                );
            }).trigger("change");

            initFormSubmission(
                $("#edit_user_form"),
                "{{ __("layout.spinner.saving") }}",
                "{{ __("user.message.fail_update") }}"
            )
        });
    </script>
@endpush
