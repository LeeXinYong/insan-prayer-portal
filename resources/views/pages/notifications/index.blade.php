<x-base-layout>
    <x-slot name="page_title_slot">{{ __("notification.index.page_title") }}</x-slot>
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
            @can("create", \App\Models\PushNotification::class)
            <!--begin::Card toolbar-->
            <div class="card-toolbar gap-3">
                @include("pages.common-components.buttons.add-button", [
                    "link" => route("notification.create"),
                    "icon" => "fs-3 ra-send"
                ])
            </div>
            <!--end::Card toolbar-->
            @endcan
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-6">
            @include("pages.common-components._table")

            @include("pages.common-components._empty-state-table", [
                "table" => $dataTable->getTableId(),
                "img" => "/demo3/customize/media/empty-states/notification.svg",
                "message" => __("empty_states.push_notifications.content"),
                "button_label" => __("empty_states.push_notifications.action"),
                "url" => Auth::user()->cannotCreate(\App\Models\PushNotification::class) ? null : route("notification.create")
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

                $("#dtSearch").keyup(function () {
                    table.search($(this).val()).draw();
                });
            });
        </script>
    @endpush
</x-base-layout>
