<x-base-layout>
    <x-slot name="page_title_slot">{{ __("download_log.page_title.index") }}</x-slot>
    <!--begin::Card-->
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <div class="d-flex flex-column flex-md-row flex-lg-column flex-xl-row align-items-center justify-content-start my-1">
                    <div class="d-flex align-items-center position-relative me-3 my-1">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                        <input type="text" name="dtSearch" id="dtSearch" class="form-control w-250px ps-15" placeholder="{{ __("general.message.search") }}">
                    </div>
                    <div class="d-flex align-items-center position-relative me-3 my-1">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen031.svg", "svg-icon-1 position-absolute ms-6 z-index-3") !!}
                        <select class="form-select w-250px ps-15" id="filterModule">
                            <option value="">{{ __("general.message.please_select") }}</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module[0] }}">{{ $module[1] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <!--end::Card title-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-6">
            @include('pages.log.download._table')
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

                $("#filterModule").select2({
                    placeholder: "{{ __("general.message.filter_by_module") }}",
                    allowClear: true,
                    width: "100%"
                }).change(function () {
                    table.draw();
                });
            });
        </script>
    @endpush
</x-base-layout>
