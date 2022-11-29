<div class="modal fade" id="{{ $modal_id ?? "view_modal" }}" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $modal_title ?? "" }}</h5>
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
                {!! $modal_body ?? "" !!}
            </div>
        </div>
    </div>
</div>
