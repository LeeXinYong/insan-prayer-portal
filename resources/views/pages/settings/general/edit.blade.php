<x-base-layout>
    <x-slot name="page_title_slot">{{ __("settings.general.page_title", []) }}</x-slot>
    <!--begin::Card-->
    <form method="post" id="edit_general_setting_form" action="{{ route('system.settings.general.update') }}">
        @csrf
        <div class="card">
            <div class="card-body">
                {{--TIMEOUT SETTINGS--}}
                <h5 class="card-title mb-2 fs-3">{{ __('settings.general.form_label.timeout_title') }}</h5>
                @include("pages.common-components._alert-dialog-hint", ["message" => __('settings.general.message.timeout_description')])
                <div class="row">
                    <div class="col-lg-4 col-form-label fw-bold fs-6">
                        <label class="form-label text-muted">{{ __('settings.general.form_label.timeout_title') }}</label>
                    </div>
                    <div class="col-lg-4 fv-row fv-plugins-icon-container">
                        <div class="d-flex flex-stack">
                            <!--begin::Switch-->
                            <div class="form-check form-switch form-check-custom form-check-solid form-check-solid-success px-switch">
                                <!--begin::Input-->
                                <input type="checkbox" name="timeout" id="timeout" class="form-check-input on-off-switch" {{ $timeout ? 'checked' : '' }} disabled>
                                <!--end::Input-->
                                <!--begin::Label-->
                                <span id="timeout_text" class="form-check-label fw-bold text-gray-700">{{ $timeout ? __('general.switch.on') : __('general.switch.off') }}</span>
                                <!--end::Label-->
                            </div>
                            <!--end::Switch-->
                        </div>
                    </div>
                </div>
                <div id="timeout-block">
                    <div class="row mt-10">
                        <div class="col-lg-4 col-form-label fw-bold fs-6">
                            <label for="timeout_duration" class="form-label text-muted">{{ __('settings.general.form_label.timeout_duration') }} <small>({{ __('settings.general.message.in_seconds') }})</small></label>
                        </div>
                        <div class="col-lg-4 fv-row fv-plugins-icon-container">
                            <input type="text" name="timeout_duration" id="timeout_duration" class="form-control" value="{{ $timeout_duration }}" placeholder="Eg: 300" disabled>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-lg-4 col-form-label fw-bold fs-6">
                            <label for="timeout_countdown" class="form-label text-muted">{{ __('settings.general.form_label.timeout_countdown') }} <small>({{ __('settings.general.message.in_seconds') }})</small></label>
                        </div>
                        <div class="col-lg-4 fv-row fv-plugins-icon-container">
                            <input type="text" name="timeout_countdown" id="timeout_countdown" class="form-control" value="{{ $timeout_countdown }}" placeholder="Eg: 60" disabled>
                        </div>
                    </div>
                </div>

                {{--GOOGLE RECAPTCHA SETTINGS--}}
                <h5 class="card-title mt-20 mb-2 fs-3">{{ __('settings.general.form_label.recaptcha_title') }}</h5>
                @include("pages.common-components._alert-dialog-hint", ["message" => __('settings.general.message.recaptcha_description')])
                <div class="row">
                    <div class="col-lg-4 col-form-label fw-bold fs-6">
                        <label class="form-label text-muted">{{ __('settings.general.form_label.recaptcha_title') }}</label>
                    </div>
                    <div class="col-lg-4 fv-row fv-plugins-icon-container">
                        <div class="d-flex flex-stack">
                            <!--begin::Switch-->
                            <div class="form-check form-switch form-check-custom form-check-solid form-check-solid-success">
                                <!--begin::Input-->
                                <input type="checkbox" name="recaptcha" id="recaptcha" class="form-check-input on-off-switch" {{ $recaptcha ? 'checked' : '' }} disabled>
                                <!--end::Input-->
                                <!--begin::Label-->
                                <span id="recaptcha_text" class="form-check-label fw-bold text-gray-700">{{ $recaptcha ? __('general.switch.on') : __('general.switch.off') }}</span>
                                <!--end::Label-->
                            </div>
                            <!--end::Switch-->
                        </div>
                    </div>
                </div>
                <div id="recaptcha-block">
                    <div class="row mt-10">
                        <div class="col-lg-4 col-form-label fw-bold fs-6">
                            <label for="recaptcha_max_attempt" class="form-label text-muted">{{ __('settings.general.form_label.recaptcha_max_login_attempts') }}</label>
                        </div>
                        <div class="col-lg-4 fv-row fv-plugins-icon-container">
                            <input type="number" name="recaptcha_max_attempt" id="recaptcha_max_attempt" class="form-control" value="{{ $recaptcha_max_attempt }}" placeholder="Eg: 1" disabled>
                        </div>
                    </div>
                </div>

                {{--FAILED BACKGROUND JOBS ALERT EMAIL AND WEBHOOK--}}
                <h5 class="card-title mt-20 mb-2 fs-3">{{ __('settings.general.form_label.failed_background_job_title') }}</h5>
                @include("pages.common-components._alert-dialog-hint", ["message" => __('settings.general.message.failed_background_job_description')])
                <div class="row">
                    <div class="col-lg-4 col-form-label fw-bold fs-6">
                        <label class="form-label text-muted mb-0">{{ __('settings.general.form_label.alert_to_email') }}</label>
                    </div>
                    <div class="col-lg-4 fv-row fv-plugins-icon-container">
                        <div class="d-flex flex-stack">
                            <!--begin::Switch-->
                            <div class="form-check form-switch form-check-custom form-check-solid form-check-solid-success">
                                <!--begin::Input-->
                                <input type="checkbox" name="failed_job_email_alert" id="failed_job_email_alert" class="form-check-input on-off-switch" {{ $failed_job_email_alert ? 'checked' : '' }} disabled>
                                <!--end::Input-->
                                <!--begin::Label-->
                                <span id="failed_job_email_alert_text" class="form-check-label fw-bold text-gray-700">{{ $failed_job_email_alert ? __('general.switch.on') : __('general.switch.off') }}</span>
                                <!--end::Label-->
                            </div>
                            <!--end::Switch-->
                        </div>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-lg-4 col-form-label fw-bold fs-6">
                        <label class="form-label text-muted mb-0">{{ __('settings.general.form_label.alert_to_webhooks') }}</label>
                    </div>
                    <div class="col-lg-4 fv-row fv-plugins-icon-container">
                        <div class="d-flex flex-stack">
                            <!--begin::Switch-->
                            <div class="form-check form-switch form-check-custom form-check-solid form-check-solid-success">
                                <!--begin::Input-->
                                <input type="checkbox" name="failed_job_webhook_alert" id="failed_job_webhook_alert" class="form-check-input on-off-switch" {{ $failed_job_webhook_alert ? 'checked' : '' }} disabled>
                                <!--end::Input-->
                                <!--begin::Label-->
                                <span id="failed_job_webhook_alert_text" class="form-check-label fw-bold text-gray-700">{{ $failed_job_webhook_alert ? __('general.switch.on') : __('general.switch.off') }}</span>
                                <!--end::Label-->
                            </div>
                            <!--end::Switch-->
                        </div>
                    </div>
                </div>

                {{--CONCURRENT_LOGIN--}}
                <h5 class="card-title mt-20 mb-2 fs-3">{{ __('settings.general.form_label.concurrent_login_title') }}</h5>
                @include("pages.common-components._alert-dialog-hint", ["message" => __('settings.general.message.concurrent_login_description')])
                <div class="row">
                    <div class="col-lg-4 col-form-label fw-bold fs-6">
                        <label class="form-label text-muted mb-0">{{ __('settings.general.form_label.web_portal') }}</label>
                    </div>
                    <div class="col-lg-4 fv-row fv-plugins-icon-container">
                        <div class="d-flex flex-stack">
                            <!--begin::Switch-->
                            <div class="form-check form-switch form-check-custom form-check-solid form-check-solid-success">
                                <!--begin::Input-->
                                <input type="checkbox" name="web_portal_concurrent_login" id="web_portal_concurrent_login" class="form-check-input on-off-switch" {{ $web_portal_concurrent_login ? 'checked' : '' }} disabled>
                                <!--end::Input-->
                                <!--begin::Label-->
                                <span id="web_portal_concurrent_login_text" class="form-check-label fw-bold text-gray-700">{{ $web_portal_concurrent_login ? __('general.switch.on') : __('general.switch.off') }}</span>
                                <!--end::Label-->
                            </div>
                            <!--end::Switch-->
                        </div>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-lg-4 col-form-label fw-bold fs-6">
                        <label class="form-label text-muted mb-0">{{ __('settings.general.form_label.mobile_app') }}</label>
                    </div>
                    <div class="col-lg-4 fv-row fv-plugins-icon-container">
                        <div class="d-flex flex-stack">
                            <!--begin::Switch-->
                            <div class="form-check form-switch form-check-custom form-check-solid form-check-solid-success">
                                <!--begin::Input-->
                                <input type="checkbox" name="mobile_app_concurrent_login" id="mobile_app_concurrent_login" class="form-check-input on-off-switch" {{ $mobile_app_concurrent_login ? 'checked' : '' }} disabled>
                                <!--end::Input-->
                                <!--begin::Label-->
                                <span id="mobile_app_concurrent_login_text" class="form-check-label fw-bold text-gray-700">{{ $mobile_app_concurrent_login ? __('general.switch.on') : __('general.switch.off') }}</span>
                                <!--end::Label-->
                            </div>
                            <!--end::Switch-->
                        </div>
                    </div>
                </div>
            </div>
            @can("update", \App\Models\SysParam::class)
            <div class="card-footer">
                <div class="d-flex align-items-center justify-content-end">
                    <div id="editBlock" class="d-block">
                        @include("pages.common-components.buttons.edit-button", [
                            "id" => "editBtn"
                        ])
                    </div>
                    <div id="submitBlock" class="d-flex align-items-center justify-content-end gap-3 d-none">
                        @include("pages.common-components.buttons.cancel-button", [
                            "id" => "cancelBtn",
                            "classes" => "btn-outline",
                            "attributes" => ""
                        ])
                        @include("pages.common-components.buttons.save-button", [
                            "id" => "submitBtn"
                        ])
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </form>
    <!--end::Card-->

    @push('scripts')
        <script>
            $(document).ready(function() {

                @can("update", \App\Models\SysParam::class)
                    initFormSubmission(
                        $("#edit_general_setting_form"),
                        "{{ __("layout.spinner.saving") }}",
                        "{{ __("settings.general.message.fail_update") }}"
                    )

                    $('#editBtn').click(function() {
                        event.preventDefault();
                        $('.form-label, .input-group-text').removeClass('text-muted');
                        $("#edit_general_setting_form").find('input, textarea, select').prop("disabled", false);
                        $('.edit-control').prop("disabled", false);
                        $('#submitBlock').removeClass('d-none').addClass('d-block');
                        $('#editBlock').removeClass('d-block').addClass('d-none');
                        timeout_control();
                        recaptcha_control();
                    });

                    $('#cancelBtn').click(function() {
                        event.preventDefault();
                        $('.form-label, .input-group-text').addClass('text-muted');
                        $("#edit_general_setting_form").trigger("reset").find('input, textarea, select').prop("disabled", true);
                        $('.edit-control').prop("disabled", true);
                        $('#editBlock').removeClass('d-none').addClass('d-block');
                        $('#submitBlock').removeClass('d-block').addClass('d-none');
                        timeout_control();
                        recaptcha_control();
                    });
                @endcan

                initSwitchLabel(
                    "#timeout",
                    "{{ __("general.switch.on") }}",
                    "{{ __("general.switch.off") }}"
                );

                initSwitchLabel(
                    "#recaptcha",
                    "{{ __("general.switch.on") }}",
                    "{{ __("general.switch.off") }}"
                );

                initSwitchLabel(
                    "#failed_job_email_alert",
                    "{{ __("general.switch.on") }}",
                    "{{ __("general.switch.off") }}"
                );

                initSwitchLabel(
                    "#failed_job_webhook_alert",
                    "{{ __("general.switch.on") }}",
                    "{{ __("general.switch.off") }}"
                );

                const timeout_control = () =>{
                    if($('#timeout').is(':checked')){
                        $('#timeout-block').show();
                    }else{
                        $('#timeout-block').hide();
                    }
                };

                const recaptcha_control = () =>{
                    if($('#recaptcha').is(':checked')){
                        $('#recaptcha-block').show();
                    }else{
                        $('#recaptcha-block').hide();
                    }
                };

                timeout_control();
                recaptcha_control();

                $('#timeout').change(function () {
                    timeout_control();
                });

                $('#recaptcha').change(function () {
                    recaptcha_control();
                });
            });
        </script>
    @endpush
</x-base-layout>
