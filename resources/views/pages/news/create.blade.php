<x-base-layout>
    <x-slot name="page_title_slot">{{ __("news.page_title.create") }}</x-slot>
    <!--begin::Form-->
    <form method="post" id="create_news_form" action="{{ route("news.store") }}" enctype="multipart/form-data">
        @csrf
        <!--begin::Card-->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label class="form-label" for="status">{{ __("news.form_label.status") }}</label>
                        <!--begin::Switch-->
                        <div class="form-check form-switch form-check-custom form-check-solid form-check-solid-success px-switch">
                            <!--begin::Input-->
                            <input type="checkbox" name="status" id="status" class="form-check-input" value="1" checked/>
                            <!--end::Input-->
                            <!--begin::Label-->
                            <span id="status_text" class="form-check-label fw-bold text-gray-700">{{ __("general.message.active") }}</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Switch-->
                        <div class="form-text text-muted">{{ __("news.message.status_msg") }}</div>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-12">
                        <label class="form-label" for="title">{{ __("news.form_label.title") }}</label>
                        <input type="text" name="title" id="title" class="form-control" required/>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-12">
                        <label class="form-label" for="thumbnail">{{ __("news.form_label.thumbnail") }}</label>
                        <input type="file" accept=".jpg, .jpeg, .png" name="thumbnail" id="thumbnail" class="form-control" required/>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-3 p-0">
                        <label class="form-label" for="url_content_switch">{{ __("news.form_label.url_content_switch") }}</label>
                        <!--begin::Switch-->
                        <div class="form-check form-switch form-check-custom form-check-solid form-check-solid-success px-switch">
                            <!--begin::Input-->
                            <input type="checkbox" name="url_content_switch" id="url_content_switch" class="form-check-input" value="1" checked/>
                            <!--end::Input-->
                            <!--begin::Label-->
                            <span id="url_content_switch_text" class="form-check-label fw-bold text-gray-700">{{ __("general.message.yes") }}</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Switch-->
                    </div>
                    <div class="col-9 p-0" >
                        <div id="url_section">
                            <label class="form-label" for="url">{{ __("news.form_label.url") }}</label>
                            <input type="url" pattern="https?://.+" name="url" id="url" class="form-control" required/>
                        </div>
                        <div id="content_section" style="display: none">
                            <label class="form-label" for="content">{{ __("news.form_label.content") }}</label>
                            @include("pages.common-components._alert-dialog-hint", ["message" => $content_info_alert])
                            <textarea name="content" id="content" class="form-control"></textarea>
                            @include("pages.common-components.buttons.preview-button", [
                                "id" => "mobile_preview_btn",
                                "classes" => "mt-2",
                                "label" => __("general.button.preview_in_mobile")
                            ])
                        </div>
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

    @include("pages.common-components._mobile-preview-modal")

    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function() {
                initImageFileInputRaw(
                    $("#thumbnail"),
                    {
                        allowedFileExtensions: ["jpg", "jpeg", "png"],
                        maxFileSize: 2000,
                        dropZoneTitle: "{{ __("news.message.drag_n_drop_thumbnail_without_limitation") }}",
                        dropZoneClickTitle: "{!! __("news.message.click_to_select_thumbnail") !!}",
                        browseLabel: "",
                        removeLabel: "",
                        showCaption: false,
                        showBrowse: false,
                        showRemove: false,
                    }
                );

                initWYSIWYG("#content", {
                    valid_children : "+body[style]",
                    paste_data_images : true,
                    forced_root_block : "",
                    force_br_newlines : true,
                    force_p_newlines : false,
                    relative_urls : false,
                    remove_script_host : false,
                    document_base_url : "{{ config("app.url") }}",
                });

                initSwitchLabel(
                    "#status",
                    "{{ __("general.message.active") }}",
                    "{{ __("general.message.inactive") }}"
                );

                initSwitchLabel(
                    "#url_content_switch",
                    "{{ __("general.message.yes") }}",
                    "{{ __("general.message.no") }}"
                );

                initFormSubmission(
                    $("#create_news_form"),
                    "{{ __("layout.spinner.creating") }}",
                    "{{ __("news.message.fail_create") }}"
                );

                // show mobile preview function
                $("#mobile_preview_btn").click(function() {
                    event.preventDefault();
                    // get current contents
                    const content = tinymce.get("content").getContent();

                    // set contents to iframe
                    const iframe = document.getElementById("mobile_preview_content");
                    const iframedoc = iframe.contentDocument || iframe.contentWindow.document;

                    // Put the content in the iframe
                    iframedoc.open();
                    iframedoc.writeln(`<br><div class="container d-flex flex-column mb-25" style="text-align: center">
                                        <small class="mt-9 mb-n9 font-weight-700">{{ __("general.modal_label.published") }}: `+moment().format('DD MMM YYYY, hh:mm A')+`</small>

                                        <h1 class="my-10 font-weight-700 narrower" style="word-break: break-word; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font-size: calc(1.3rem + 0.6vw); margin-top: 5px; margin-bottom: 5px;">
                                            `+$('#title').val()+`
                                        </h1>

                                        <div style="text-align: left">
                                            <div class="pb-5 narrower" id="resource-content">`+unescape(content)+`</div>
                                        </div>
                                    </div>`);
                    iframedoc.close();

                    // open modal
                    $("#mobile_preview_modal").modal('show');
                });

                $("#url_content_switch").click(function() {
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

