<x-base-layout>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <x-slot name="page_title_slot">{{ __("ip_whitelist.page_title.index") }}</x-slot>

    {{-- <div class="p-2">    
        <ol class="breadcrumb text-muted fs-6 fw-semibold">
            <li class="breadcrumb-item pe-3"><a href="{{ theme()->getPageUrl('') }}" class="pe-3">Dashboard</a></li>
            <li class="breadcrumb-item pe-3 text-muted">IP Whitelist</li>
        </ol>
    </div> --}}

    <!--begin::Card-->
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 text-dark position-absolute ms-6") !!}
                    <input type="text" name="dtSearch" id="dtSearch" class="form-control w-250px ps-15" placeholder="{{ __("general.message.search") }}">
                </div>
            </div>
            <!--end::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar gap-3">
                @can("arrange", \App\Models\IpWhitelist::class)
                    @include("pages.common-components.buttons.rearrange-items-buttons")
                @endcan

                @can("create", \App\Models\IpWhitelist::class)
                    @include("pages.common-components.buttons.add-button", [
                        "link" => route("ip_whitelist.create")
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
                "message" => __("empty_states.default.content"),
                "button_label" => __("empty_states.default.action"),
                "url" => Auth::user()->cannotCreate(\App\Models\IpWhitelist::class) ? null : route("ip_whitelist.create")
            ])
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->

    @include("pages.common-components._mobile-preview-modal")

    {{-- Inject Scripts --}}
    @push("scripts")
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
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
