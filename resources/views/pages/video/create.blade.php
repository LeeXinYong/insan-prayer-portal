<x-base-layout>
    <x-slot name="page_title_slot">{{ __("video.page_title.create") }}</x-slot>
    <!--begin::Form-->
    <form method="post" id="create_video_form" class="upload_form" action="{{ route("video.store") }}" enctype="multipart/form-data">
        @csrf
        <!--begin::Card-->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label class="form-label" for="status">{{ __("video.form_label.status") }}</label>
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
                        <div class="form-text text-muted">{{ __("video.message.status_msg") }}</div>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-6">
                        <label class="form-label" for="video_type">{{ __("video.form_label.video_type") }}</label>
                        <select name="video_type" id="video_type" class="form-select" required>
                            <option value="upload" selected>{{ __("video.dropdown_option.upload_video") }}</option>
                            <option value="youtube">{{ __("video.dropdown_option.youtube") }}</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="video_title">{{ __("video.form_label.video_title") }}</label>
                        <input type="text" name="video_title" id="video_title" class="form-control" required>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-12" id="video_section">
                        {{-- Video Upload --}}
                        <div id="upload_section">
                            <label class="form-label mb-0" id="video_label" for="video_file">{{ __("video.form_label.video") }}</label>
                            <input type="file" accept=".mp4" name="video_file" id="video_file" class="form-control" required>
                        </div>
                        {{-- Youtube --}}
                        <div id="youtube_section" style="display: none;">
                            <label class="form-label mb-0" id="youtube_label" for="youtube_url">{{ __("video.form_label.youtube_url") }}</label>
                            <div class="input-group">
                                <input type="text" name="youtube_url" id="youtube_url" class="form-control" placeholder="{{ __("video.form_placeholder.youtube_url") }}">
                                <div class="input-group-append">
                                    @include("pages.common-components.buttons.custom-button", [
                                        "id" => "fetch_youtube_data_btn",
                                        "size" => "",
                                        "classes" => "rounded-start-0",
                                        "icon" => "fs-3 fa fa-search",
                                        "label" => __("video.button.fetch_button")
                                    ])
                                </div>
                            </div>
                            <input type="hidden" name="youtube_video_id" id="youtube_video_id" value="">
                        </div>
                        <div class="d-flex">
                            <label class="form-control text-muted border-0 p-0 w-unset me-2">{{ __("video.form_label.duration") . ":" }}</label>
                            <input id="duration" name="duration" class="form-control text-muted border-0 p-0" value="{{ __("video.message.default_duration_input") }}" readonly>
                        </div>
                    </div>
                    <div id="thumbnail_section" class="col-6" style="display: none;">
                        <div class="row">
                            <div class="col-12">
                                <div class="w-100 d-flex justify-content-between align-items-center gap-4 h-60px h-sm-45px">
                                    <div class="flex-grow-1 d-flex flex-column gap-1">
                                        <label class="form-label" for="thumbnail_switch">{{ __("video.form_label.use_auto_generated_thumbnail") }}</label>
                                    </div>
                                    <div class="flex-grow-1 d-flex flex-column gap-1">
                                        <!--begin::Switch-->
                                        <div class="form-check form-switch form-check-custom form-check-solid form-check-solid-success px-switch">
                                            <!--begin::Input-->
                                            <input type="checkbox" name="thumbnail_switch" id="thumbnail_switch" class="form-check-input" value="1" checked/>
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Switch-->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div id="auto_thumbnail_section">
                                    <img id="thumbnail_preview" class="w-100" src="" alt="">
                                    <input type="hidden" name="youtube_thumbnail_link" id="youtube_thumbnail_link" value="">
                                    <input type="hidden" name="auto_thumbnail" id="auto_thumbnail">
                                    <div class="row mt-3 align-items-center" id="video_time_slider">
                                        <div class="col-4"><input type="text" class="form-control text-center" id="video_time"></div>
                                        <div class="col-8"><div id="video_slider"></div></div>
                                    </div>
                                </div>

                                <div id="manual_thumbnail_section" style="display: none;">
                                    <input type="file" accept=".jpg, .jpeg, .png" id="manual_thumbnail" name="manual_thumbnail" class="form-control">
                                </div>
                            </div>
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

    {{-- Inject Scripts --}}
    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function (){
                $("#video_type").select2({
                    placeholder: "{{ __("general.message.please_select") }}",
                    allowClear: false,
                    minimumResultsForSearch: -1,
                    width: "100%"
                });

                initImageFileInputRaw(
                    $("#video_file"),{
                        allowedFileExtensions: ["mp4"],
                        maxFileSize: 50000,
                        dropZoneTitle: "{{ __("video.message.drag_drop_video") }}",
                        dropZoneClickTitle: "{!! __("video.message.click_to_select_video") !!}",
                        browseLabel: "",
                        removeLabel: "",
                        showCaption: false,
                        showBrowse: false,
                        showRemove: false,
                    }
                );

                initImageFileInputRaw(
                    $('#manual_thumbnail'),
                    {
                        allowedFileExtensions: ['jpg', 'jpeg', 'png'],
                        maxFileSize: 2000,
                        dropZoneTitle: "{{ __("video.message.drag_drop_thumbnail") }}",
                        dropZoneClickTitle: "{!! __("video.message.click_to_select_thumbnail") !!}",
                        browseLabel: "",
                        removeLabel: "",
                        showCaption: false,
                        showBrowse: false,
                        showRemove: false,
                    }
                );

                initSwitchLabel(
                    "#thumbnail_switch",
                );
                initSwitchLabel(
                    "#status",
                    "{{ __("general.message.active") }}",
                    "{{ __("general.message.inactive") }}"
                );

                initVideoForm(
                    {},
                    "{{ __("video.form_label.use_auto_generated_thumbnail") }}",
                    "{{ __("video.message.file_constraints") }}"
                );

                initFetchYoutubeAPI(
                    {},
                    {
                        title: "{{ __("video.message.fetch_new_youtube_confirmation") }}",
                        html: "{!! __("video.message.fetch_new_youtube_msg") !!}",
                        confirmButtonText: "{{ __("general.button.confirm") }}",
                        cancelButtonText: "{{ __("general.button.cancel") }}"
                    },
                    "{{ route("video.fetchData") }}",
                    "{{ __("video.message.fetch_youtube_api_required_msg") }}",
                    "{{ __("video.form_label.use_auto_generated_thumbnail") }}"
                );

                initFormSubmission(
                    $("#create_video_form"),
                    "{{ __("layout.spinner.creating") }}",
                    "{{ __("video.message.fail_create") }}"
                );

            })
        </script>
    @endpush
</x-base-layout>
