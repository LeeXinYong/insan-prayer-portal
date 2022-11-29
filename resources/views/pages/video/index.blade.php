<x-base-layout>
    <x-slot name="page_title_slot">{{ __("video.page_title.index") }}</x-slot>
    <!--begin::Card-->
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header pt-6">
            <!--begin::Card title-->
            <div class="card-title gap-4">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                    <input type="text" name="dtSearch" id="dtSearch" class="form-control w-250px ps-15" placeholder="{{ __("general.message.search") }}">
                </div>
                <div class="d-flex align-items-center position-relative me-3 my-1">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen031.svg", "svg-icon-1 position-absolute ms-6 z-index-3") !!}
                    <select class="form-select w-250px ps-15" id="filterVideoType">
                        <option value="">{{ __("general.message.please_select") }}</option>
                        <option value="upload">{{ __("video.dropdown_option.upload_video") }}</option>
                        <option value="youtube">{{ __("video.dropdown_option.youtube") }}</option>
                    </select>
                </div>
            </div>
            <!--end::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar gap-3">
                @can("arrange", \App\Models\Video::class)
                    @include("pages.common-components.buttons.rearrange-items-buttons")
                @endcan

                @can("create", \App\Models\Video::class)
                    @include("pages.common-components.buttons.add-button", [
                        "link" => route("video.create")
                    ])
                @endcan
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-6">
            @include("pages.common-components._table")

            @include("pages.common-components._empty-state-table", [
                "table" => $dataTable->getTableId(),
                "img" => "/demo3/customize/media/empty-states/document.svg",
                "message" => __("empty_states.video.content"),
                "button_label" => __("empty_states.video.action"),
                "url" => Auth::user()->cannotCreate(\App\Models\Video::class) ? null : route("video.create")
            ])
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->

    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function () {

                const table = window.{{ config("datatables-html.namespace", "LaravelDataTables") }}["{{ $dataTable->getTableId() }}"];

                $("#filterVideoType").select2({
                    placeholder: "{{ __("general.message.all") }}",
                    minimumResultsForSearch: -1,
                    allowClear: true,
                }).on("change.select2", function (e) {
                    table.draw();
                });

                $("#dtSearch").keyup(function () {
                    table.search($(this).val()).draw();
                });
            });
        </script>
    @endpush
</x-base-layout>
