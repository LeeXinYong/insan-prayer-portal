<x-base-layout>
    <x-slot name="page_title_slot">{{__("video.page_title.edit")}}</x-slot>
    <!--begin::Form-->
    <form method="post" id="edit_video_form" class="upload_form" action="{{ route("video.update", ["video" => $video->id]) }}" enctype="multipart/form-data">
        @csrf
        @method("PUT")
        <!--begin::Card-->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label class="form-label" for="status">{{ __("video.form_label.status") }}</label>
                        <!--begin::Switch-->
                        <label class="form-check form-switch form-check-custom px-switch form-check-solid form-check-solid-success">
                            <!--begin::Input-->
                            <input type="checkbox" name="status" id="status" class="form-check-input" value="1" {{ $video->status ? "checked" : "" }}/>
                            <!--end::Input-->
                            <!--begin::Label-->
                            <span id="status_text" class="form-check-label fw-bold text-gray-700">{{ $video->status ? __("general.message.active") : __("general.message.inactive") }}</span>
                            <!--end::Label-->
                        </label>
                        <!--end::Switch-->
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-6">
                        <label class="form-label" for="video_type">{{__("video.form_label.video_type")}}</label>
                        <select name="video_type" id="video_type" class="form-select" required>
                            <option value="upload" @if($video->video_type == "upload") selected @endif>{{ __("video.dropdown_option.upload_video") }}</option>
                            <option value="youtube" @if($video->video_type == "youtube") selected @endif>{{ __("video.dropdown_option.youtube") }}</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="title">{{__("video.form_label.video_title")}}</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ $video->title }}" required>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-6" id="video_section">
                        {{-- Video Upload --}}
                        <div id="upload_section" style="{{ ($video->video_type == "upload") ? "" : "display: none" }}">
                            <label class="form-label mb-0 h-60px h-sm-45px" id="video_label" for="video_file">{{ __("video.form_label.video") }}</label>
                            <input type="file" accept=".mp4" name="video_file" id="video_file" class="form-control">
                        </div>
                        {{-- Youtube --}}
                        <div id="youtube_section" style="{{ ($video->video_type == "youtube") ? "" : "display: none" }}">
                            <label class="form-label mb-0 h-60px h-sm-45px" id="youtube_label" for="youtube_url">{{ __("video.form_label.youtube_url") }}</label>
                            <div class="input-group">
                                <input type="text" name="youtube_url" id="youtube_url" class="form-control" placeholder="{{ __("video.form_placeholder.youtube_url") }}" value="{{ $video->youtube_url }}" {{ ($video->video_type == "youtube") ? "required" : "" }}>
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
                            <input type="hidden" name="youtube_video_id" id="youtube_video_id" value="{{ $video->youtube_video_id }}">
                        </div>
                        <div class="d-flex">
                            <label class="form-control text-muted border-0 p-0 w-unset me-2">{{ __("video.form_label.duration") . ":" }}</label>
                            <input id="duration" name="duration" class="form-control text-muted border-0 p-0" value="{{ $video->duration ?? __("video.message.default_duration_input") }}" readonly>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-12">
                                <div class="w-100 d-flex justify-content-between align-items-center gap-4 h-60px h-sm-45px">
                                    <div class="flex-grow-1 d-flex flex-column gap-1">
                                        <label class="form-label" id="thumbnail_switch_label" for="thumbnail_switch">{{ __("video.form_label.use_current_thumbnail") }}</label>
                                    </div>
                                    <div class="flex-grow-1 d-flex flex-column gap-1">
                                        <!--begin::Switch-->
                                        <label class="form-check form-switch form-check-custom form-check-solid form-check-solid-success px-switch">
                                            <!--begin::Input-->
                                            <input type="checkbox" name="thumbnail_switch" id="thumbnail_switch" class="form-check-input" value="1" checked/>
                                            <!--end::Input-->
                                        </label>
                                        <!--end::Switch-->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div id="current_thumbnail">
                                    <img src="{{ route("getFile", ["file_module" => "video", "module_id" => $video->id, "file_path_field" => "thumbnail_path"]) }}" class="img-fluid border w-100" alt="" />
                                </div>
                            </div>
                        </div>

                        <div id="thumbnail_section" style="display: none;">
                            <div class="row">
                                <div class="col-12">
                                    <div id="auto_thumbnail_section">
                                        <img id="thumbnail_preview" class="w-100" src="" alt="">
                                        <input type="hidden" name="youtube_thumbnail_link" id="youtube_thumbnail_link" value="{{ $video->youtube_thumbnail_link }}">
                                        <input type="hidden" name="auto_thumbnail" id="auto_thumbnail" value=""/>
                                        <input type="hidden" name="new_video_upload" id="new_video_upload" value="0">
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
            </div>
            <div class="card-footer">
                <div class="d-flex align-items-center @can("delete", \App\Models\Video::class) justify-content-between @else justify-content-end @endcan gap-3">
                    @can("delete", \App\Models\Video::class)
                        @include("pages.common-components.buttons.delete-button", [
                            "indicator" => true,
                            "id" => "del_btn"
                        ])
                    @endcan
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
    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function (){
                $("#video_type").select2({
                    width: "100%",
                    minimumResultsForSearch: -1,
                });

                let file_url = []
                let file_config = [];
                @if(!empty($video->file_path))
                file_url.push("{{ route("getFile", ["file_module" => "video", "module_id" => $video->id, "file_path_field" => "file_path", "file_name" => strtolower(pathinfo($video->file_path, PATHINFO_EXTENSION))]) }}");
                file_config.push({
                    type: "video",
                    caption: "{{ $video->file_name }}",
                    size: "{{ $video->getOriginal("file_size") }}",
                    filetype: "video/mp4",
                });
                @endif

                initImageFileInputRaw(
                    $("#video_file"),{
                        allowedFileExtensions: ["mp4"],
                        maxFileSize: 50000,
                        dropZoneTitle: "{{ __("video.message.drag_drop_video") }}",
                        dropZoneClickTitle: "{!! __("video.message.click_to_select_video") !!}",
                        browseLabel: "",
                        removeLabel: "",
                        initialPreview: file_url,
                        initialPreviewAsData: file_url.length > 0,
                        initialPreviewConfig: file_config,
                        overwriteInitial: file_url.length > 0,
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
                    "",
                    "{{$video->video_type}}"
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
                    $("#edit_video_form"),
                    "{{ __("layout.spinner.saving") }}",
                    "{{ __("video.message.fail_update") }}"
                );

                initDelete(
                    "#del_btn",
                    {
                        title: "{{ __("general.message.confirmation") }}",
                        html: "{!! __("video.message.video_delete_msg", ["video_title" => $video->title]) !!}",
                        confirmButtonText: "{{ __("general.button.delete") }}",
                        cancelButtonText: "{{ __("general.button.cancel") }}"
                    },
                    "{{ __("layout.spinner.deleting") }}",
                    "{{ route("video.destroy", ["video" => $video->id]) }}",
                    "{{ csrf_token() }}"
                )
            });
        </script>
    @endpush
</x-base-layout>
