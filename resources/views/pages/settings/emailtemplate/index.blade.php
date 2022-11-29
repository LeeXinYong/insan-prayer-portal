<x-base-layout>
    <x-slot name="page_title_slot">{{ __("settings.email_template.page_title.index") }}</x-slot>

    <!--begin::Card-->
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header pt-6">
            @include("pages.common-components._alert-dialog-hint", ["message" => "<ul>".array_reduce(__("settings.email_template.message.hints"), function($carry, $hint) { return $carry."<li>$hint</li>"; }, "")."</ul>"])
            <!--begin::Card title-->
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                    <input type="text" name="dtSearch" id="dtSearch" class="form-control w-250px ps-15" placeholder="{{ __("general.message.search") }}">
                </div>
            </div>
            <!--end::Card title-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-6">
            @include("pages.common-components._table")
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
