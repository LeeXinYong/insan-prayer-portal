<x-base-layout>
    <x-slot name="page_title_slot">{{ __($model["name"] . ".page_title.edit") }}</x-slot>
    <!--begin::Form-->
    <form method="post" id="edit_{{ $model["name"] }}_form" action="{{ route($model["name"] . ".update", [$model["name"] => $model["instance"]->id]) }}" enctype="multipart/form-data">
    @csrf
    @method("PUT")
    <!--begin::Card-->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label class="form-label" for="status">{{ __($model["name"] . ".form_label.status") }}</label>
                        <!--begin::Switch-->
                        <div class="form-check form-switch form-check-custom form-check-solid form-check-solid-success px-switch">
                            <!--begin::Input-->
                            <input type="checkbox" name="status" id="status" class="form-check-input" value="1" {{ $model["instance"]->status ? "checked" : "" }}/>
                            <!--end::Input-->
                            <!--begin::Label-->
                            <span id="status_text" class="form-check-label fw-bold text-gray-700">{{ $model["instance"]->status ? __("general.message.active") : __("general.message.inactive") }}</span>
                            <!--end::Label-->
                        </div>
                        <!--end::Switch-->
                        <div class="form-text text-muted">{{ __($model["name"] . ".message.status_msg") }}</div>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-12">
                        <label class="form-label" for="title">{{ __($model["name"] . ".form_label.title") }}</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ $model["instance"]->title }}" required/>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-6" id="file_upload_section">
                        <label class="form-label mb-0 h-60px h-sm-45px" id="pdf_file_label" for="pdf_file">{{ __($model["name"] . ".form_label.pdf_file") }}</label>
                        <input type="file" accept=".pdf" name="pdf_file" id="pdf_file" class="form-control"/>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-12">
                                <div class="w-100 d-flex justify-content-between align-items-center gap-4">
                                    <div class="flex-grow-1 d-flex flex-column gap-1">
                                        <label class="form-label" for="thumbnail_switch">{{ __($model["name"] . ".form_label.thumbnail_switch") }} @include('partials.tooltips.faq-tooltip', ["title" =>  __($model["name"] . ".message.auto_generated_thumbnail_msg") ])</label>
                                    </div>
                                    <div class="flex-grow-1 d-flex flex-column gap-1">
                                        <!--begin::Switch-->
                                        <div class="form-check form-switch form-check-custom form-check-solid form-check-solid-success px-switch">
                                            <!--begin::Input-->
                                            <input type="checkbox" name="thumbnail_switch" id="thumbnail_switch" class="form-check-input" value="1" checked/>
                                            <!--end::Input-->
                                            <!--begin::Label-->
                                            <span id="thumbnail_switch_text" class="form-check-label fw-bold text-gray-700">{{ __("general.message.yes") }}</span>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Switch-->
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div id="current_thumbnail" class="row">
                                    <div class="col-12 d-flex justify-content-center">
                                        <img src="{{ route("getFile", ["file_module" => $model["name"], "module_id" => $model["instance"]->id, "file_path_field" => "thumbnail_path", "file_name" => strtolower(pathinfo($model["instance"]->thumbnail_path, PATHINFO_EXTENSION))]) }}" class="img-fluid border" style="max-width: 300px" alt=""/>
                                    </div>
                                </div>
                                <div class="row" id="thumbnail" style="display: none">
                                    <div class="col-12">
                                        <div id="auto_thumbnail_section">
                                            <div id="canvas_div">
                                                <canvas id="the-canvas" class="border" style="display: none;" width="248" height="351"></canvas>
                                            </div>
                                            <img id="cropper_preview" class="border" alt="" src="" style="max-width: 100%; height: 343px;">
                                            <input type="hidden" name="auto_thumbnail" id="auto_thumbnail"/>
                                            <input type="hidden" id="new_upload" value="0">
                                        </div>
                                        <div id="manual_thumbnail_section" style="display: none">
                                            <input type="file" accept=".jpg, .jpeg, .png" name="manual_thumbnail" id="manual_thumbnail" class="form-control"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex align-items-center @can("delete", $model["class"]::class) justify-content-between @else justify-content-end @endcan gap-3">
                    @can("delete", $model["class"]::class)
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
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function() {
                let file_url = []
                let file_config = []
                @if(!empty($model["instance"]->file_path))
                file_url.push("{{ route("getFile", ["file_module" => $model["name"], "module_id" => $model["instance"]->id, "file_path_field" => "file_path", "file_name" => strtolower(pathinfo($model["instance"]->file_path, PATHINFO_EXTENSION))]) }}");
                file_config.push({
                    type: "pdf",
                    caption: "{{ $model["instance"]->file_name }}",
                    size: "{{ $model["instance"]->getOriginal("file_size") }}",
                })
                @endif
                initImageFileInputRaw(
                    $("#pdf_file"),
                    {
                        allowedFileExtensions: ["pdf"],
                        maxFileSize: 50000,
                        dropZoneTitle: "{{ __($model["name"] . ".message.drag_n_drop_file_without_limitation") }}",
                        dropZoneClickTitle: "{!! __($model["name"] . ".message.click_to_select_file") !!}",
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
                    "#status",
                    "{{ __("general.message.active") }}",
                    "{{ __("general.message.inactive") }}"
                );
                initSwitchLabel(
                    "#thumbnail_switch",
                    "{{ __("general.message.yes") }}",
                    "{{ __("general.message.no") }}"
                );

                $("#thumbnail_switch").change(function() {
                    if ($(this).is(":checked")) {
                        $(" #tooltip ").attr('data-bs-original-title', "{{ __($model["name"] . ".message.auto_generated_thumbnail_msg") }}");
                    } else {
                        $(" #tooltip ").attr('data-bs-original-title', "{{ __($model["name"] . ".message.manual_upload_thumbnail_msg") }}");
                    }
                });

                initFormSubmission(
                    $("#edit_{{ $model["name"] }}_form"),
                    "{{ __("layout.spinner.saving") }}",
                    "{{ __($model["name"] . ".message.fail_update") }}"
                )

                initDelete(
                    "#del_btn",
                    {
                        title: "{{ __("general.message.confirmation") }}",
                        html: "{!! __($model["name"] . ".message.delete_msg", ["title" => $model["instance"]->title]) !!}",
                        confirmButtonText: "{{ __("general.button.delete") }}",
                        cancelButtonText: "{{ __("general.button.cancel") }}"
                    },
                    "{{ __("layout.spinner.deleting") }}",
                    "{{ route($model["name"] . ".destroy", [$model["name"] => $model["instance"]->id]) }}",
                    "{{ csrf_token() }}"
                )
            });
        </script>
    @endpush
</x-base-layout>

