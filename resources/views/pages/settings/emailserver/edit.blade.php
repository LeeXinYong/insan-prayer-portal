<x-base-layout>
    <x-slot name="page_title_slot">{{ __("settings.email_server.page_title", []) }}</x-slot>

    <!--begin::Card-->
    <form method="post" id="edit_emailserver_form" action="{{ ($id != "" || $id != null) ? route('system.settings.emailserver.update', ["emailserver" => $id]) : route('system.settings.emailserver.store') }}">
        @csrf
        @if($id != "" || $id != null)
            @method("PUT")
        @endif
        <div class="card">
            <div class="card-body">
                @include("pages.common-components._alert-dialog-hint", ["message" => __("settings.email_server.message.hint")])
                {{--MAIL DRIVER SELECTION--}}
                <div class="row">
                    <div class="col-6">
                        <label for="transport" class="form-label text-muted">{{ __("settings.email_server.form_label.send_via", []) }}</label>
                        <select name="transport" id="transport" class="form-select" required disabled>
                            <option value="">{{ __('general.message.please_select') }}</option>
                            <option value="SMTP" @if($transport==='SMTP')selected @endif>{{ __("settings.email_server.form_label.smtp", []) }}</option>
                            <option value="MAILGUN_API" @if($transport==='MAILGUN_API')selected @endif>{{ __("settings.email_server.form_label.mailgun_api", []) }}</option>
                        </select>
                    </div>
                </div>

                {{--SMTP--}}
                <div class="row mt-10 smtp-form-group">
                    <div class="col-6">
                        <label for="name" class="form-label text-muted">{{ __("settings.email_server.form_label.host", []) }}</label>
                        <input type="text" name="host" id="host" class="form-control" value="{{ $host }}" placeholder="Eg: smtp.gmail.com for Gmail" required="required" disabled>
                    </div>
                    <div class="col-6">
                        <label for="email" class="form-label text-muted">{{ __("settings.email_server.form_label.port", []) }}</label>
                        <input type="text" name="port" id="port" class="form-control" value="{{ $port }}" placeholder="Eg: 587 or 465 for Gmail" required="required" disabled>
                    </div>
                </div>
                <div class="row mt-10 smtp-form-group">
                    <div class="col-6">
                        <label for="name" class="form-label text-muted">{{ __("settings.email_server.form_label.encryption_type", []) }}</label>
                        <select class="form-select" name="encryption" id="encryption" required="required" disabled>
                            <option value="">{{ __('general.message.please_select') }}</option>
                            @if ($enc == "tls")
                                <option value="tls" selected>{{ __("settings.email_server.form_label.tls", []) }}</option>
                                <option value="ssl">{{ __("settings.email_server.form_label.ssl", []) }}</option>
                            @elseif ($enc == "ssl")
                                <option value="tls">{{ __("settings.email_server.form_label.tls", []) }}</option>
                                <option value="ssl" selected>{{ __("settings.email_server.form_label.ssl", []) }}</option>
                            @else
                                <option value="tls">{{ __("settings.email_server.form_label.tls", []) }}</option>
                                <option value="ssl">{{ __("settings.email_server.form_label.ssl", []) }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="row mt-10 smtp-form-group">
                    <div class="col-6">
                        <label for="name" class="form-label text-muted">{{ __("settings.email_server.form_label.username", []) }}</label>
                        <input type="text" autocomplete="false" name="username" id="username" class="form-control" value="{{ $username }}" required="required" disabled>
                    </div>
                    <div class="col-6">
                        <label for="email" class="form-label text-muted">{{ __("settings.email_server.form_label.password", []) }}</label>
                        <input type="password" autocomplete="false" name="password" id="password" class="form-control" required="required" disabled>
                    </div>
                </div>

                {{--MAILGUN API--}}
                <div class="row mt-10 mailgun-api-form-group">
                    <div class="col-6">
                        <label for="domain" class="form-label text-muted">{{ __("settings.email_server.form_label.mailgun.domain", []) }}</label>
                        <input type="text" autocomplete="false" name="domain" id="domain" class="form-control" value="{{ $domain }}" disabled>
                    </div>
                    <div class="col-6">
                        <label for="secret" class="form-label text-muted">{{ __("settings.email_server.form_label.mailgun.api_key", []) }}</label>
                        <input type="password" autocomplete="false" name="secret" id="secret" class="form-control" disabled>
                    </div>
                </div>

                {{--REQUIRED FOR ALL DRIVER--}}
                <div class="row mt-10">
                    <div class="col-6">
                        <label for="name" class="form-label text-muted">{{ __("settings.email_server.form_label.sender_name", []) }}</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $name }}" placeholder="Eg: Lisa from abc.com" required="required" disabled>
                    </div>
                    <div class="col-6">
                        <label for="address" class="form-label text-muted">{{ __("settings.email_server.form_label.sender_address", []) }}</label>
                        <input type="text" name="address" id="address" class="form-control" value="{{ $address }}" placeholder="Eg: hello@abc.com" required="required" disabled>
                    </div>
                </div>

                @if(false)
                <!-- Hide first, no need -->
                <div class="form-group row">
                    <label class="col-2 col-form-label" for="cc">Cc</label>
                    <div class="col-10">
                        <input type="email" name="cc" id="cc" class="form-control" placeholder="Optional Cc email address">
                    </div>
                </div>
                @endif
            </div>

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
        </div>
    </form>
    <!--end::Card-->

    @push('scripts')
        <script>
            $(document).ready(function() {

                initFormSubmission(
                    $("#edit_emailserver_form"),
                    "{{ __("layout.spinner.saving") }}",
                    "{{ __("settings.email_server.message.fail_update") }}"
                )

                var transport = '{{$transport}}' ;
                controlFormGroup();

                $('#transport').change(function() {
                    transport = $(this).children("option:selected").val();
                    controlFormGroup();
                });

                function controlFormGroup() {
                    if(transport==='SMTP'){
                        // Hide/Show Group
                        $('.smtp-form-group').show();
                        $('.mailgun-api-form-group').hide();

                        // Required input
                        $('#host').attr('required', true);
                        $('#port').attr('required', true);
                        $('#encryption').attr('required', true);
                        $('#username').attr('required', true);
                        $('#password').attr('required', true);
                        $('#domain').attr('required', false);
                        $('#secret').attr('required', false);
                    }else if(transport==='MAILGUN_API'){
                        // Hide/Show Group
                        $('.smtp-form-group').hide();
                        $('.mailgun-api-form-group').show();

                        // Required input
                        $('#host').attr('required', false);
                        $('#port').attr('required', false);
                        $('#encryption').attr('required', false);
                        $('#username').attr('required', false);
                        $('#password').attr('required', false);
                        $('#domain').attr('required', true);
                        $('#secret').attr('required', true);
                    }else{
                        // Hide All Group
                        $('.smtp-form-group').hide();
                        $('.mailgun-api-form-group').hide();

                        // Required input
                        $('#host').attr('required', false);
                        $('#port').attr('required', false);
                        $('#encryption').attr('required', false);
                        $('#username').attr('required', false);
                        $('#password').attr('required', false);
                        $('#domain').attr('required', false);
                        $('#secret').attr('required', false);
                    }
                }


                $('#editBtn').click(function() {
                    event.preventDefault();
                    $('.form-label, .input-group-text').removeClass('text-muted');
                    $("form").find('input, textarea, select').prop("disabled", false);
                    $('#submitBlock').removeClass('d-none').addClass('d-block');
                    $('#editBlock').removeClass('d-block').addClass('d-none');
                });

                $('#cancelBtn').click(function() {
                    event.preventDefault();
                    $('.form-label, .input-group-text').addClass('text-muted');
                    $("form").find('input, textarea, select').prop("disabled", true);
                    $('#editBlock').removeClass('d-none').addClass('d-block');
                    $('#submitBlock').removeClass('d-block').addClass('d-none');
                    transport = '{{$transport}}' ;
                    controlFormGroup();
                });

            });
        </script>
    @endpush

</x-base-layout>
