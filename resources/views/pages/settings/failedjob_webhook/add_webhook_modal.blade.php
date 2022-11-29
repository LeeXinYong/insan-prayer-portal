<!--begin::Modal - Add Failed Job Webhook-->
<div class="modal fade" tabindex="-1">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2 class="modal-title"></h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                @include("pages.common-components.buttons.close-button", [
                    "size" => "btn-icon",
                    "attributes" => "data-bs-toggle=modal data-bs-label=close",
                    "icon" => "fs-6 ra-cancel",
                    "icon_only" => true
                ])
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Form-->
            <form class="form" method="post" id="addFailedJobWebhookForm">
                @csrf
                <!--begin::Modal body-->
                <div class="modal-body scroll-y py-8 px-9">
                    <div class="row fv-row fv-plugins-icon-container">
                        <div class="col-12">
                            <!--begin::Label-->
                            <label for="webhook_url" class="form-label">{{ __('settings.failed_job_webhook.create.webhook_url') }}</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input class="form-control form-control-solid" name="webhook_url" id="webhook_url" type="url" pattern="https?://.+">
                            <div class="invalid-feedback"><strong id="webhook_url_error"></strong></div>
                            <!--end::Input-->
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
                <!--begin::Modal footer-->
                <div class="modal-footer flex-end gap-3">
                    @include("pages.common-components.buttons.cancel-button", [
                        "classes" => "btn-outline",
                        "attributes" => "data-bs-dismiss=modal"
                    ])
                    @include("pages.common-components.buttons.create-button", [
                        "indicator" => true,
                        "label" => __("general.button.add"),
                        "message" => __("general.button.adding")
                    ])
                </div>
                <!--end::Modal footer-->
            </form>
            <!--end::Form-->
        </div>
    </div>
</div>
<!--end::Modal - Add Failed Job Webhook-->
