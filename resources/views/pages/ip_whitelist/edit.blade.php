<x-base-layout>
    {{-- <div class="p-2">    
        <ol class="breadcrumb text-muted fs-6 fw-semibold">
            <li class="breadcrumb-item pe-3"><a href="{{ theme()->getPageUrl('') }}" class="pe-3">Dashboard</a></li>
            <li class="breadcrumb-item pe-3"><a href="javascript:history.back()" class="pe-3">IP Whitelist</a></li>
            <li class="breadcrumb-item pe-3 text-muted">Edit</li>
        </ol>
    </div> --}}
    <x-slot name="page_title_slot">{{ __("ip_whitelist.page_title.edit") }}</x-slot>
    <!--begin::Form-->
    <form method="post" id="edit_ip_whitelist_form" action="{{ route("ip_whitelist.update", ["ip_whitelist" => $ip_whitelist->ip_address]) }}" enctype="multipart/form-data">
        @csrf
        @method("PUT")
        <!--begin::Card-->
        <div class="card">
            <div class="card-header align-items-center border-0 mt-4">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">{{ $ip_whitelist->ip_address }}</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="row mt-10">
                    <div class="col-12 form-floating">
                        <input type="text" name="ip_description" id="ip_description" class="form-control" placeholder="{{ $ip_whitelist->ip_description }}" value="{{ $ip_whitelist->ip_description }}" required/>
                        <label class="form-label" for="ip_description">{{ __("ip_whitelist.form_label.ip_description") }}</label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex align-items-center @can("delete", \App\Models\IpWhitelist::class) justify-content-between @else justify-content-end @endcan gap-3">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        @include("pages.common-components.buttons.cancel-button", [
                            "classes" => "btn-outline"
                        ])
                        @include("pages.common-components.buttons.save-button", [
                            "indicator" => true
                        ])
                    </div>
                </div>
            </div>
        </div>
        <!--end::Card-->
    </form>
    <!--end::Form-->

    {{-- Inject Scripts --}}
    @push("scripts")

        <script type="text/javascript">
            $(document).ready(function() {

                initFormSubmission(
                    $("#edit_ip_whitelist_form"),
                    "{{ __("layout.spinner.saving") }}",
                    "{{ __("ip_whitelist.message.fail_update") }}"
                );

                $("#url_content_switch").click(function () {
                    if ($(this).prop("checked")) {
                        $("#content_section").hide();
                        $("#url_section").show();
                        $("#url").prop("required", true);
                    } else {
                        $("#url_section").hide();
                        $("#url").prop("required", false);
                        $("#content_section").show();
                    }
                });
            });
        </script>
    @endpush
</x-base-layout>

