<x-base-layout>
    <x-slot name="page_title_slot">{{ __("user.page_title.index") }}</x-slot>
    <!--begin::Card-->
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <div class="d-flex flex-column flex-md-row align-items-start justify-content-start my-1">
                    <div class="d-flex align-items-center position-relative my-1 me-3">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 text-dark position-absolute ms-6") !!}
                        <input type="text" name="dtSearch" id="dtSearch" class="form-control w-250px ps-15" placeholder="{{ __("general.message.search") }}">
                    </div>

                    <div class="d-flex align-items-center position-relative me-3 my-1">
                        @include("pages.common-components.buttons.hover-buttons.hover-button", [
                            "color" => "btn-custom-secondary btn-active-custom-light",
                            "size" => "btn-icon",
                            "attributes" => "data-kt-menu-trigger=click data-kt-menu-placement=bottom-start",
                            "icon" => "fs-1 las la-filter",
                            "icon_only" => true
                        ])
                        <div class="menu menu-sub menu-sub-dropdown" data-kt-menu="true">
                            <!--begin::Form-->
                            <div class="px-7 py-3">
                                <form class="form">
                                    @foreach($roles as $role)
                                        <div class="w-100 d-flex align-items-center justify-content-between gap-5 {{ !$loop->last ? "mb-3" : "" }}">
                                            <label class="form-label fw-semibold fs-6 mb-0">{{ $role->name }}</label>
                                            <!--begin::Options-->
                                            <div class="d-flex align-items-center justify-content-end">
                                                <!--begin::Option-->
                                                <div class="form-check form-check-custom form-check-solid px-switch">
                                                    <input type="checkbox" name="role_filter[]" class="form-check-input role_filter" value="{{ $role->name }}" checked/>
                                                </div>
                                                <!--end::Option-->
                                            </div>
                                            <!--end::Options-->
                                        </div>
                                    @endforeach
                                </form>
                            </div>
                            <!--end::Form-->
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                @can("create", \App\Models\User::class)
                    @include("pages.common-components.buttons.add-button", [
                        "link" => route("user.create")
                    ])
                @endcan
            </div>
            <!--end::Card toolbar-->
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

                var roleFilterApplied = $('.role_filter:checked').map(function(){
                                                return $(this).val();
                                            }).get();
                $(".role_filter").change(function () {
                    table.draw();
                });
            });
        </script>
    @endpush
</x-base-layout>
