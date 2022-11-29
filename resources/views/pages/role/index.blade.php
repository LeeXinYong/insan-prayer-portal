<x-base-layout>
    <x-slot name="page_title_slot">{{ __("role.index.page_title") }}</x-slot>
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
            @can("create", \Spatie\Permission\Models\Role::class)
            <!--begin::Card toolbar-->
            <div class="card-toolbar gap-3">
                @include("pages.common-components.buttons.add-button", [
                    "attributes" => "data-bs-toggle=modal data-bs-target=#roleModal"
                ])
            </div>
            <!--end::Card toolbar-->
            @endcan
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-6">
            @include("pages.common-components._table")
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->

    @includeWhen(\Illuminate\Support\Facades\Auth::user()->canCreate(\Spatie\Permission\Models\Role::class), "pages.role.add_or_edit_role_modal")

    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function () {
                const table = window.{{ config("datatables-html.namespace", "LaravelDataTables") }}["{{ $dataTable->getTableId() }}"];

                $("#dtSearch").keyup(function () {
                    table.search($(this).val()).draw();
                });

                @can("create", \Spatie\Permission\Models\Role::class)
                AddOrEditRoleName.EditRole({
                    modalTitle: "{{ __("role.index.addRole.label") }}",
                    submitButtonText: "{{ __("general.button.add") }}",
                    cancelButtonText: "{{ __("general.button.cancel") }}",
                    resultButtonText: "{{ __("general.button.ok") }}",
                    validatorMessage : {
                        name : {
                            required : "{{ __("role.index.addRole.validator.name.required") }}"
                        }
                    },
                    formSubmission: {
                        url: "{{ route("role.store") }}",
                    },
                    initCallback: function (modal) {
                        new ColorPicker(document.getElementById('color'));
                    },
                    persistValues: false,
                    successCallback: function(response) {
                        table.draw();
                    },
                });
                @endcan
            });
        </script>
    @endpush
</x-base-layout>
