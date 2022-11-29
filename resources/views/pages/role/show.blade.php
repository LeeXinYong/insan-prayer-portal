<x-base-layout>
    {{--    <x-slot name="page_title_slot">{{ __("role.show.page_title") }}</x-slot>--}}
    <!--begin::Card-->
    <div class="card mb-6">
        <div class="card-body">
            <div class="card-header border-0 p-0 mb-4 min-h-unset">
                <div class="card-title my-0">
                    <h3 class="font-weight-bolder" id="role_name">{{ $role->name }}</h3>
                    @can("update", $role)
                        @include("pages.common-components.buttons.edit-button", [
                            "id" => "downloadLog",
                            "color" => "btn-custom-light btn-active-custom-light",
                            "size" => "btn-icon",
                            "attributes" => "data-bs-toggle=modal data-bs-target=#roleModal",
                            "icon" => "fs-3 ra-edit",
                            "icon_only" => true
                        ])
                    @endcan
                </div>
                @can("delete", $role)
                    @include("pages.common-components.buttons.delete-button", ["label" => __("role.show.delete_role.label"), "id" => "delete-role-button"])
                @endcan
            </div>
            <div class="d-none align-items-start justify-content-between" id="select-all-wrapper">
                <!--begin::Switch-->
                <label
                    class="form-check form-switch form-check-custom form-check-solid px-switch align-items-start pt-0 gap-4">
                    <!--begin::Input-->
                    <input type="checkbox" name="status" id="status" class="form-check-input w-50px h-35px"
                           value="1" {{ old("status", 1) == 1 ? "checked" : "" }}
                    />
                    <!--end::Input-->
                    <div>
                        <!--begin::Label-->
                        <span id="status_text"
                              class="form-check-label fw-bold text-gray-700">{{ old("status",  1) == 1 ? __("role.show.status_switch.select_all") : __("role.show.status_switch.deselect_all") }}</span>
                        <!--end::Label-->
                        <div id="status_description"
                             class="ms-2 text-muted">{{ old("status",  1) == 1 ? __("role.show.status_switch.select_all_permissions") : __("role.show.status_switch.deselect_all_permissions") }}</div>
                    </div>
                </label>
                <!--end::Switch-->
            </div>
        </div>
    </div>
    <!--end::Card-->

    <x-loader id="permissions-loader">
        <!--begin::Form-->
        <form method="post" id="update-permissions-form" action="{{ route("role.update.permissions", ["role" => $role]) }}"
              class="w-100"
              enctype="multipart/form-data">
            @csrf
            @method("PUT")
            <div class="d-grid gap-6" style="grid-template-columns: repeat(3, 1fr)" id="permissions-wrapper"></div>

            <div class="separator my-8 border-gray-400"></div>

            <div class="w-100 d-flex align-items-center justify-content-end gap-4">
                @include("pages.common-components.buttons.cancel-button", ["classes" => "btn-outline"])
                @include("pages.common-components.buttons.save-button", ["id" => "update-permissions-form-save-button", "attributes" => "style='display: none'"])
            </div>
        </form>
    </x-loader>


    @includeWhen(\Illuminate\Support\Facades\Auth::user()->canUpdate($role), "pages.role.add_or_edit_role_modal")

    {{-- Inject Scripts --}}
    @include('pages.role.permission-template')
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function () {

                const selectAllPermissionWrapper = $("#select-all-wrapper");
                const permissionsLoader = $("#permissions-loader");
                const permissionsWrapper = $("#permissions-wrapper");
                const saveButton = $("#update-permissions-form-save-button");

                axios.get('{{ route('role.show', ['role' => $role]) }}')
                    .then(function (response) {
                        const data = response.data;
                        if (data.noPermissionUpdatable) {
                            selectAllPermissionWrapper.removeClass("d-flex").addClass("d-none");
                            saveButton.hide();
                        } else {
                            selectAllPermissionWrapper.removeClass("d-none").addClass("d-flex");
                            saveButton.show();
                        }

                        permissionsWrapper.empty();
                        (Object.values(data.permissions) || []).forEach(function (permission) {
                            permissionsWrapper.append(getPermissionTemplate(permission));
                        });

                        Array.from(document.querySelectorAll('.master-checkbox')).forEach((master) => {
                            const slaves = Array.from(document.querySelectorAll('.slave-checkbox[data-target="' + master.id + '"]'));
                            new MasterSlaveCheckbox({master: master, slaves: slaves});
                            [master, ...slaves].forEach(ele => {
                                ele.addEventListener("change", _ => {
                                    document.querySelector('.count-span[data-target="' + master.id + '"]').innerText = ' (' + slaves.filter(slave => slave.checked).length + '/' + slaves.length + ')';
                                })
                            })
                        });

                        const masterSwitch = new MasterSlaveCheckbox({
                            master: document.getElementById("status"),
                            slaves: Array.from(document.querySelectorAll('.master-checkbox, .slave-checkbox')),
                            masterStyling: false,
                        });

                        document.getElementById("status").addEventListener("change", _ => {
                            Array.from(document.querySelectorAll('.master-checkbox')).forEach(masterCheckbox => masterCheckbox.dispatchEvent(new Event('change')));
                        });

                        initSwitchLabel(
                            "#status",
                            "{{ __("role.show.status_switch.select_all") }}",
                            "{{ __("role.show.status_switch.deselect_all") }}"
                        );

                        initSwitchLabel(
                            "#status",
                            "{{ __("role.show.status_switch.select_all_permissions") }}",
                            "{{ __("role.show.status_switch.deselect_all_permissions") }}",
                            "#status_description"
                        );

                        initFormSubmission(
                            $("#update-permissions-form"),
                            "{{ __("layout.spinner.saving") }}",
                            "{{ __("role.show.update_permissions.failed") }}"
                        )

                        permissionsLoader.attr("data-kt-indicator", "off");
                    });



                @can("update", $role)
                // Edit Name
                AddOrEditRoleName.EditRole({
                    modalTitle: "{{ __("role.show.editName.label") }}",
                    submitButtonText: "{{ __("general.button.edit") }}",
                    cancelButtonText: "{{ __("general.button.cancel") }}",
                    resultButtonText: "{{ __("general.button.ok") }}",
                    validatorMessage: {
                        name: {
                            required: "{{ __("role.show.editName.validator.name.required") }}"
                        }
                    },
                    defaultValues: {
                        name: $("#role_name").text(),
                        color: '{{ $role->color }}',
                    },
                    initCallback: function (modal) {
                        new ColorPicker(document.getElementById('color'), {
                            defaultColor: '{{ $role->color }}',
                        });
                    },
                    formSubmission: {
                        url: "{{ route("role.update", $role->id) }}",
                        method: 'PUT',
                    },
                    successCallback: function (response) {
                        $("#role_name").text(response.data.data.name);
                    },
                });
                @endcan

                @can("delete", $role)
                initDelete(
                    "#delete-role-button",
                    {
                        title: "{{ __("general.message.confirmation") }}",
                        html: "{{ __("role.show.delete_role.confirm") }}",
                        confirmButtonText: "{{ __("general.button.delete") }}",
                        cancelButtonText: "{{ __("general.button.cancel") }}"
                    },
                    "{{ __("layout.spinner.deleting") }}",
                    "{{ route("role.destroy", ["role" => $role]) }}",
                    "{{ csrf_token() }}",
                )
                @endcan
            });
        </script>
    @endpush
</x-base-layout>
