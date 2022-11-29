<div class="modal fade" id="mobile_preview_modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __("general.modal_label.mobile_preview") }}</h5>
                <!--begin::Close-->
                @include("pages.common-components.buttons.close-button", [
                    "size" => "btn-icon",
                    "attributes" => "data-bs-toggle=modal data-bs-label=close",
                    "icon" => "fs-6 ra-cancel",
                    "icon_only" => true
                ])
                <!--end::Close-->
            </div>
            <div class="modal-body">
                <div style="text-align: center;">
                    <div class="marvel-device iphone-x">
                        <div class="notch">
                            <div class="camera"></div>
                            <div class="speaker"></div>
                        </div>
                        <div class="top-bar"></div>
                        <div class="sleep"></div>
                        <div class="bottom-bar"></div>
                        <div class="volume"></div>
                        <div class="overflow">
                            <div class="shadow shadow--tr"></div>
                            <div class="shadow shadow--tl"></div>
                            <div class="shadow shadow--br"></div>
                            <div class="shadow shadow--bl"></div>
                        </div>
                        <div class="inner-shadow"></div>
                        <div class="screen">
                            <div class="status-bar"></div>
                            <!-- Content goes here -->
                            <iframe id="mobile_preview_content" src="" style="width:100%;border:none;height:100%"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
