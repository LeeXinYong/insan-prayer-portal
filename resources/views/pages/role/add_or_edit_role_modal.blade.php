<div class="modal fade" id="roleModal" tabindex="-1">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2 id="roleModalTitle"></h2>
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
            <form id="roleModalForm" class="form fv-plugins-bootstrap5 fv-plugins-framework" enctype="multipart/form-data" novalidate>
                @csrf
                <!--begin::Modal body-->
                <div class="modal-body scroll-y py-8 px-9">
                    <div class="d-flex flex-column gap-4">
                        <div class="row fv-row fv-plugins-icon-container">
                            <div class="col-12">
                                <!--begin::Label-->
                                <label for="name" class="form-label">{{ __("role.addOrEditRoleModal.label.name") }}</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" class="form-control form-control-solid" placeholder="" id="name" name="name">
                                <!--end::Input-->
                            </div>
                        </div>
                        <div class="row fv-row fv-plugins-icon-container">
                            <div class="col-12">
                                <!--begin::Label-->
                                <label for="color" class="form-label">{{ __("role.addOrEditRoleModal.label.color") }}</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                {{--                            <div class="h-30px w-30px rounded-circle border border-gray-400" onclick="this.children[0].click()">--}}
                                {{--                                <input type="color" class="invisible" placeholder="" id="color" name="color" oninput="this.parentNode.style.backgroundColor = this.value">--}}
                                {{--                            </div>--}}
                                <input type="color" class="form-control form-control-solid" placeholder="" id="color" name="color">
                                <!--end::Input-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-end gap-3">
                    @include("pages.common-components.buttons.cancel-button", [
                        "id" => "roleModalFormCancelButton",
                        "classes" => "btn-outline",
                        "attributes" => "data-bs-dismiss=modal"
                    ])
                    @include("pages.common-components.buttons.save-button", [
                        "indicator" => true,
                        "id" => "roleModalFormSubmitButton"
                    ])
                </div>
            </form>
            <!--end::Form-->
        </div>
    </div>
</div>
