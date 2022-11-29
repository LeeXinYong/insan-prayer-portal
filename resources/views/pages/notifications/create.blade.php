<x-base-layout>
    <x-slot name="page_title_slot">{{ __("notification.create.page_title") }}</x-slot>
    <!--begin::Form-->
    <form method="post" id="create_notification_form" action="{{ route("notification.store") }}" enctype="multipart/form-data">
        @csrf
        <!--begin::Card-->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <label class="form-label" for="title">{{ __("notification.create.form_label.title") }}</label>
                        <input type="text" name="title" id="title" class="form-control" required/>
                        <span class="text-success mt-2 mb-n5 w-100 d-flex justify-content-end h6" id="title-count-div">
                            <span id="title-count">0</span>/{{ \App\Services\PushNotificationService::SUGGESTED_MAX_TITLE_COUNT }}
                        </span>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-12">
                        <label class="form-label" for="message">{{ __("notification.create.form_label.message") }}</label>
                        <textarea type="text" name="message" id="message" class="form-control" required rows="3"></textarea>
                        <span class="text-success mt-2 mb-n5 w-100 d-flex justify-content-end h6" id="message-count-div">
                            <span id="message-count">0</span>/{{ \App\Services\PushNotificationService::SUGGESTED_MAX_MESSAGE_COUNT }}
                        </span>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6">
                        <label class="form-label" for="action">{{__("notification.create.form_label.action")}}</label>
                        <select name="action" id="action" class="form-select" required>
                            @foreach($actions as $actionKey => $action )
                                <option value="{{ $actionKey }}" data-target-label="{{ $action->name }}" @if($loop->first) selected @endif>{{ __("notification.create.actions.".$action->name ) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6" style="display: none">
                        <label class="form-label" for="action-target"></label>
                        <select name="action_target" id="action-target" class="form-select">
                            <option value="">{{ __("general.message.please_select") }}</option>
                        </select>
                    </div>
                </div>
                @if(\Illuminate\Support\Facades\App::make(\App\Services\PushNotificationService::class)->isImageEnabled() || \Illuminate\Support\Facades\App::make(\App\Services\PushNotificationService::class)->isLargeIconEnabled())
                    <div class="row mt-10">
                    @if(\Illuminate\Support\Facades\App::make(\App\Services\PushNotificationService::class)->isImageEnabled())
                        <div class="col-6">
                            <label class="form-label" for="image">{{ __("notification.create.form_label.image") }} <span class="fs-9 fst-italic">({{ __("general.form_label.optional") }})</span></label>
                            <input type="file" accept=".jpg, .jpeg, .png" name="image" id="image" class="form-control"/>
                        </div>
                    @endif
                    @if(\Illuminate\Support\Facades\App::make(\App\Services\PushNotificationService::class)->isLargeIconEnabled())
                        <div class="col-6">
                            <label class="form-label" for="icon">{{ __("notification.create.form_label.icon") }} <span class="fs-9 fst-italic">({{ __("general.form_label.optional") }})</span></label>
                            <input type="file" accept=".jpg, .jpeg, .png" name="icon" id="icon" class="form-control"/>
                        </div>
                    @endif
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    @include("pages.common-components.buttons.cancel-button", [
                        "classes" => "btn-outline"
                    ])
                    @include("pages.common-components.buttons.test-button", [
                        "indicator" => true,
                        "id" => "sent-to-test-recipients",
                        "label" => __("notification.create.button.send_to_test_recipients"),
                        "message" => __("notification.create.button.sending_to_test_recipients")
                    ])
                    @include("pages.common-components.buttons.create-button", [
                        "indicator" => true,
                        "icon" => "fs-3 ra-send",
                        "label" => __("general.button.send"),
                        "message" => __("general.button.sending")
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
            $(document).ready(function () {
                const tier = $("#tier");
                const action = $("#action");
                const actionTarget = $("#action-target");
                const actionTargetLabel = $("label[for='action-target']");
                tier.select2({
                    minimumResultsForSearch: -1,
                    allowClear: false,
                    width: '100%',
                });
                action.select2({
                    minimumResultsForSearch: -1,
                    allowClear: false,
                    width: '100%',
                });
                actionTarget.select2({
                    ajax: {
                        url: '{{ route("notification.create") }}',
                        data: function (params) {
                            return {
                                action: action.val(),
                                search: params.term,
                            };
                        },
                        dataType: 'json',
                        processResults: function (data, params) {
                            console.log(data);
                            return {
                                results: data.data.map(datum => {return {id: datum.id, text: datum.title};}),
                            };
                        }
                    },
                    placeholder: "{{ __("general.message.please_select") }}",
                    minimumResultsForSearch: 5,
                    allowClear: false,
                    width: '100%',
                });
                action.change(function () {
                    actionTarget.empty();
                    if ([
                        "{{ \App\Models\Enums\PushNotificationAction::Default->name }}"
                    ].includes(action.val())) {
                        actionTarget.attr('required', false);
                        actionTarget.parent().hide();
                    } else {
                        actionTarget.attr('required', true);
                        actionTarget.parent().show();

                        actionTargetLabel.text(action.find(':selected').data('target-label'));

                        actionTarget.val("").trigger("change");
                    }
                });

                const maxTitleCount = {{ \App\Services\PushNotificationService::SUGGESTED_MAX_TITLE_COUNT }};
                const maxMessageCount = {{ \App\Services\PushNotificationService::SUGGESTED_MAX_MESSAGE_COUNT }};
                const titleCountDiv = $("#title-count-div");
                const messageCountDiv = $("#message-count-div");
                const titleInput = $("input[name='title']");
                const messageTextarea = $("textarea[name='message']");
                const updateTitleCount = function () {
                    let titleCount = titleInput.val().length;

                    $("#title-count").text(titleCount);

                    if (titleCount > maxTitleCount) {
                        titleCountDiv.removeClass("text-success").addClass("text-danger");
                    } else {
                        titleCountDiv.removeClass("text-danger").addClass("text-success");
                    }
                }
                const updateMessageCount = function () {
                    let messageCount = messageTextarea.val().length;

                    $("#message-count").text(messageCount);

                    if (messageCount > maxMessageCount) {
                        messageCountDiv.removeClass("text-success").addClass("text-danger");
                    } else {
                        messageCountDiv.removeClass("text-danger").addClass("text-success");
                    }
                }
                titleInput.on("change keyup", updateTitleCount);
                messageTextarea.on("change keyup", updateMessageCount);
                updateTitleCount();
                updateMessageCount();

                @if(\Illuminate\Support\Facades\App::make(\App\Services\PushNotificationService::class)->isImageEnabled())
                initImageFileInputRaw(
                    $("#image"),
                    {
                        allowedFileExtensions: ["jpg", "jpeg", "png"],
                        maxFileSize: 2000,
                        dropZoneTitle: "{{ __("notification.create.message.drag_n_drop") }}",
                        dropZoneClickTitle: "{!! __("notification.create.message.click_to_select") !!}",
                        browseLabel: "",
                        removeLabel: "",
                        showCaption: false,
                        showBrowse: false,
                        showRemove: false
                    }
                );
                @endif


                @if(\Illuminate\Support\Facades\App::make(\App\Services\PushNotificationService::class)->isLargeIconEnabled())
                initImageFileInputRaw(
                    $("#icon"),
                    {
                        allowedFileExtensions: ["jpg", "jpeg", "png"],
                        maxFileSize: 2000,
                        dropZoneTitle: "{{ __("notification.create.message.drag_n_drop") }}",
                        dropZoneClickTitle: "{!! __("notification.create.message.click_to_select") !!}",
                        browseLabel: "",
                        removeLabel: "",
                        showCaption: false,
                        showBrowse: false,
                        showRemove: false
                    }
                );
                @endif

                initFormSubmission(
                    $("#create_notification_form"),
                    "{{ __("layout.spinner.sending") }}",
                    "{{ __("notification.create.message.failed_to_send_notification") }}",
                    {
                        submitButton: $("#sent-to-test-recipients"),
                        url: "{{ route("notification.test") }}",
                        useFormOnSubmit: false,
                        useButtonOnClick: true,
                        callbacks: {
                            beforeSubmit: async function () {
                                let text = [];
                                if (titleInput.val().length > maxTitleCount) {
                                    text.push("<span class='text-danger fs-6'><i class='la la-exclamation-triangle me-2 fs-4'></i>{{ __("notification.create.message.title_too_long", ["character" => \App\Services\PushNotificationService::SUGGESTED_MAX_TITLE_COUNT]) }}</span>");
                                }
                                if (messageTextarea.val().length > maxMessageCount) {
                                    text.push("<span class='text-danger fs-6'><i class='la la-exclamation-triangle me-2 fs-4'></i>{{ __("notification.create.message.message_too_long", ["character" => \App\Services\PushNotificationService::SUGGESTED_MAX_MESSAGE_COUNT]) }}</span>");
                                }
                                if (text.length > 0) {
                                    text.push("<br>{{ __("notification.create.message.you_can_still_send_the_notification") }}");
                                }

                                let result = await Swal.fire({
                                    title: "{{ __("notification.create.message.are_you_sure_to_send_the_notification_to_test_recipients") }}",
                                    html: text.join("<br>"),
                                    showCancelButton: true,
                                    reverseButtons: true,
                                    confirmButtonText: "{{ __("general.button.send") }}",
                                    cancelButtonText: "{{ __("general.button.cancel") }}",
                                    customClass: {
                                        popup: "swal2-info",
                                        confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                                        cancelButton: "btn btn-custom-light btn-active-custom-light min-w-60px min-w-lg-150px min-h-40px rounded-1 btn-outline"
                                    },
                                });

                                return result.isConfirmed;
                            }
                        }
                    }
                );

                initFormSubmission(
                    $("#create_notification_form"),
                    "{{ __("layout.spinner.sending") }}",
                    "{{ __("notification.create.message.failed_to_send_notification") }}",
                    {
                        callbacks: {
                            beforeSubmit: async function () {
                                let text = [];
                                if (titleInput.val().length > maxTitleCount) {
                                    text.push("<span class='text-danger fs-6'><i class='la la-exclamation-triangle me-2 fs-4'></i>{{ __("notification.create.message.title_too_long", ["character" => \App\Services\PushNotificationService::SUGGESTED_MAX_TITLE_COUNT]) }}</span>");
                                }
                                if (messageTextarea.val().length > maxMessageCount) {
                                    text.push("<span class='text-danger fs-6'><i class='la la-exclamation-triangle me-2 fs-4'></i>{{ __("notification.create.message.message_too_long", ["character" => \App\Services\PushNotificationService::SUGGESTED_MAX_MESSAGE_COUNT]) }}</span>");
                                }
                                if (text.length > 0) {
                                    text.push("<br>{{ __("notification.create.message.you_can_still_send_the_notification") }}");
                                }
                                let result = await Swal.fire({
                                    title: "{{ __("notification.create.message.are_you_sure_to_send_the_notification") }}",
                                    html: text.join("<br>"),
                                    showCancelButton: true,
                                    reverseButtons: true,
                                    confirmButtonText: "{{ __("general.button.send") }}",
                                    cancelButtonText: "{{ __("general.button.cancel") }}",
                                    customClass: {
                                        popup: "swal2-warning",
                                        confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                                        cancelButton: "btn btn-custom-light btn-active-custom-light min-w-60px min-w-lg-150px min-h-40px rounded-1 btn-outline"
                                    },
                                });

                                return result.isConfirmed;
                            }
                        }
                    }
                );
            });
        </script>
    @endpush
</x-base-layout>
