<x-base-layout>
    <x-slot name="page_title_slot">{{ __("notification.manage_test_recipients.page_title") }}</x-slot>
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

                // Update Permissions
                $(document).on("change", ".test-recipients-checkbox", function () {
                    const checkBox = $(this);
                    const userId = checkBox.val();
                    const userName = checkBox.data("name");
                    const grantingTestRecipient = checkBox.is(":checked");

                    const url = "{{ route("notification.testRecipients.update", ["user" => ":user"]) }}".replace(":user", userId);

                    Swal.fire({
                        title: (grantingTestRecipient ? "{{ __("notification.manage_test_recipients.are_you_sure_to_make_user_test_recipient") }}" : "{{ __("notification.manage_test_recipients.are_you_sure_to_remove_user_test_recipient") }}").replace(":user", userName),
                        icon: "warning",
                        showCancelButton: true,
                        reverseButtons: true,
                        buttonsStyling: false,
                        confirmButtonText: "{{ __("general.message.yes") }}",
                        cancelButtonText: "{{ __("general.button.cancel") }}",
                        customClass: {
                            popup: "swal2-warning",
                            confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                            cancelButton: "btn btn-custom-light btn-active-custom-light min-w-60px min-w-lg-150px min-h-40px rounded-1 btn-outline"
                        }
                    }).then(function (result) {
                        if (result.value) {
                            SpinnerSingletonFactory.block();

                            axios.post(url, {
                                make_test_recipient: grantingTestRecipient
                            }).then(function (response) {
                                toastr.success(response.data.message);
                            }).catch(function (error) {
                                const errorMessage = error.response?.data?.message || (error.response?.data?.errors !== undefined ? Object.values(error.response?.data?.errors)?.[0]?.[0] : (error.response?.data?.error || "{{ __("general.message.please_try_again") }}"));
                                toastr.error(errorMessage);
                                checkBox.prop("checked", !grantingTestRecipient);
                            })
                                .finally(function () {
                                    SpinnerSingletonFactory.unblock();
                                });

                        } else {
                            checkBox.prop("checked", !grantingTestRecipient);
                        }
                    });
                });
            });
        </script>
    @endpush
</x-base-layout>
