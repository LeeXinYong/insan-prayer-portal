<x-base-layout>
    <x-slot name="page_title_slot">{{ __("role.users.page_title") }}</x-slot>
    <!--begin::Card-->
    <div class="card mb-6">
        <div class="card-header card-header-stretch">
            <div class="card-title">
                <h3 class="font-weight-bolder" id="role_name">{{ $role->name }}</h3>
            </div>
        </div>
        <div class="card-body py-9">
            <div class="d-flex align-items-center position-relative me-3 my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 text-dark position-absolute ms-6") !!}
                <input type="text" name="dtSearchUser" id="dtSearchUser" class="form-control form-control-solid w-250px ps-15" placeholder="{{ __("general.message.search") }}">
            </div>
            @include("pages.common-components._table")
        </div>
    </div>
    <!--end::Card-->

    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function () {
                const userTable = window.{{ config("datatables-html.namespace", "LaravelDataTables") }}["{{ $dataTable->getTableId() }}"];

                $("#dtSearchUser").keyup(function () {
                    userTable.search($(this).val()).draw();
                });

                $(document).on("change", ".user-checkbox", function () {
                    const checkBox = $(this);
                    const userReference = checkBox.data("reference");
                    const grantingUser = checkBox.is(":checked");

                    Swal.fire({
                        title: grantingUser ? "{{ __("role.users.change_user.grant_role") }}" : "{{ __("role.users.change_user.revoke_role") }}",
                        text: grantingUser ? "{{ __("role.users.change_user.are_you_sure_to_grant_role") }}" : "{{ __("role.users.change_user.are_you_sure_to_revoke_role") }}",
                        showCancelButton: true,
                        reverseButtons: true,
                        buttonsStyling: false,
                        confirmButtonText: grantingUser ? "{{ __("role.users.change_user.grant") }}" : "{{ __("role.users.change_user.revoke") }}",
                        cancelButtonText: "{{ __("general.button.cancel") }}",
                        customClass: {
                            popup: "swal2-warning",
                            confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                            cancelButton: "btn btn-custom-light btn-active-custom-light min-w-60px min-w-lg-150px min-h-40px rounded-1 btn-outline"
                        }
                    }).then(function (result) {
                        if (result.value) {
                            SpinnerSingletonFactory.block(grantingUser ? "{{ __("role.users.change_user.granting_role") }}" : "{{ __("role.users.change_user.revoking_role") }}");

                            axios.put("{{ route("role.update.users", ["role" => $role]) }}", {
                                userReference: userReference,
                                grantingUser: grantingUser
                            }).then(function (response) {
                                toastr.success(response.data.message);
                            }).catch(function (error) {
                                const errorMessage = error.response?.data?.message || (error.response?.data?.errors !== undefined ? Object.values(error.response?.data?.errors)?.[0]?.[0] : (error.response?.data?.error || "{{ __("general.message.please_try_again") }}"));
                                toastr.error(errorMessage);
                                checkBox.prop("checked", !grantingUser);
                            })
                                .finally(function () {
                                    SpinnerSingletonFactory.unblock();
                                });

                        } else {
                            checkBox.prop("checked", !grantingUser);
                        }
                    });
                });
            });
        </script>
    @endpush
</x-base-layout>
