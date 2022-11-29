<x-base-layout>
    <x-slot name="page_title_slot">{{ __($model["name"] . ".page_title.index") }}</x-slot>
    <!--begin::Card-->
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                    <input type="text" name="dtSearch" id="dtSearch" class="form-control w-250px ps-15" placeholder="{{ __("general.message.search") }}">
                </div>
            </div>
            <!--end::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar gap-3">
                @can("arrange", $model["class"]::class)
                    @include("pages.common-components.buttons.rearrange-items-buttons")
                @endcan

                @can("create", $model["class"]::class)
                    @include("pages.common-components.buttons.add-button", [
                        "link" => route($model["name"] . ".create")
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
                "message" => __("empty_states.".$model["name"].".content"),
                "button_label" => __("empty_states.".$model["name"].".action"),
                "url" => Auth::user()->cannotCreate($model["class"]::class) ? null : route($model["name"] . ".create")
            ])
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->

    <iframe src="" id="pdf_frame" style="display: none;"
            width="1920px"
            height="1080px"
            frameBorder="0"
            allow="autoplay; fullscreen"
            allowFullScreen>
    </iframe>


    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function () {
                const table = window.{{ config("datatables-html.namespace", "LaravelDataTables") }}["{{ $dataTable->getTableId() }}"];

                $("#dtSearch").keyup(function () {
                    table.search($(this).val()).draw();
                });
            });
        </script>
    @endpush
</x-base-layout>
