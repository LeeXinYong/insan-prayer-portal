<x-base-layout>
    <x-slot name="page_title_slot">{{ __("settings.failed_job_webhook.page_title") }}</x-slot>

    @include("pages.common-components._alert-dialog-hint", ["color" => "primary", "message" => "<ul>".array_reduce(__("settings.failed_job_webhook.message.hints"), function($carry, $hint) { return $carry."<li>$hint</li>"; }, "")."</ul>", "icon" => "icons/duotune/general/gen045.svg"])

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
            <div class="card-toolbar">
            @can("create", \App\Models\FailedJobWebhook::class)
                @include("pages.common-components.buttons.add-button", [
                    "id" => "addFailedJobWebhookButton"
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

    @can('create', \App\Models\FailedJobWebhook::class)
        @include('pages.settings.failedjob_webhook.add_webhook_modal')
    @endcan

    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function () {
                const table = window.{{ config("datatables-html.namespace", "LaravelDataTables") }}["{{ $dataTable->getTableID() }}"];
                $("#dtSearch").keyup(function () {
                    table.search($(this).val()).draw();
                });

                @can("create", \App\Models\FailedJobWebhook::class)
                $("#addFailedJobWebhookButton").click(function () {
                    AddOrEditModal.AddModal({
                        modalTitle: "{{ __("settings.failed_job_webhook.create.add_new_webhook") }}",
                        modalFormSelector: '#addFailedJobWebhookForm',
                        submitButtonSelector: '#addFailedJobWebhookForm [type="submit"]',
                        cancelButtonSelector: '#addFailedJobWebhookForm [type="button"]',
                        submitButtonText: "{{ __("general.button.add") }}",
                        cancelButtonText: "{{ __("general.button.cancel") }}",
                        resultButtonText: "{{ __("general.button.ok") }}",
                        formValidation: {
                            "webhook_url": {
                                validators: {
                                    notEmpty: {
                                        message: "{{ __("settings.failed_job_webhook.create.validation.webhook_url.required") }}"
                                    },
                                    uri: {
                                        message: "{{ __("settings.failed_job_webhook.create.validation.webhook_url.pattern") }}"
                                    }
                                }
                            },
                        },
                        formSubmission: {
                            url: "{{ route('system.settings.failed_job_webhook.store') }}",
                            method: "POST",
                        },
                        persistValues: false,
                        successCallback: function (response) {
                            table.draw();
                        },
                    });
                });
                @endcan

                $('#{{ $dataTable->getTableID() }}').on("click", ".edit-control, .delete-control", function() {
                    event.preventDefault();
                    event.stopPropagation();

                    const id = $(this).data('eid');
                    const title = $(this).attr('data-bs-original-title') + "?";
                    const text = $(this).data('text');
                    const url = $(this).data('action');
                    const method = ($(this).data('method')) ? $(this).data('method') : 'post';

                    const swal_color = $(this).hasClass('delete-control') ? 'danger' : 'warning';

                    // fix issue where this button is still focused
                    $(this).blur();
                    fireSwal(
                        swal_color,
                        title,
                        null,
                        false,
                        "{{ __("general.message.yes") }}",
                        "{{ __("general.button.cancel") }}",
                        onConfirm = function() {
                            SpinnerSingletonFactory.block();

                            axios.defaults.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
                            axios({
                                method: method,
                                url: url
                            }).
                            then(function (response) {
                                toastr.success(response.data.message);
                                // reload datatables
                                window.{{ config("datatables-html.namespace", "LaravelDataTables") }}["{{ $dataTable->getTableID() }}"].ajax.reload();
                            }).catch(function (error) {
                                console.log(error);
                                const errorMessage = error.response?.data?.message || (error.response?.data?.errors !== undefined ? Object.values(error.response?.data?.errors)?.[0]?.[0] : (error.response?.data?.error || "{{ __("general.message.please_try_again") }}"));
                                toastr.error(errorMessage);
                            })
                                .finally(function () {
                                    SpinnerSingletonFactory.unblock();
                                });
                        }
                    );
                })
            });
        </script>
    @endpush

</x-base-layout>
