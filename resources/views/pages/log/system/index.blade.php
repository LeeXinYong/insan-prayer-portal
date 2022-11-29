<x-base-layout>

    <!--begin::Card-->
    <div class="card">

        <!--begin::Card header-->
        <div class="card-header card-header-stretch">
            <!--begin::Card title-->
            <div class="card-title">
                <div class="d-flex flex-column flex-sm-row flex-lg-column flex-xl-row justify-content-start">
                    <div class="d-flex align-items-center position-relative me-3 my-1">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 text-dark position-absolute ms-6") !!}
                        <input type="text" name="dtSearch" id="dtSearch" class="form-control w-250px ps-15" placeholder="{{ __("general.message.search") }}">
                    </div>
                    <div class="d-flex align-items-center position-relative me-3 my-1">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen031.svg", "svg-icon-1 position-absolute ms-6 z-index-3") !!}
                        <select class="form-select w-250px ps-15" id="filterFile">
                            <option value="">{{ __("general.message.please_select") }}</option>
                            @foreach($logFiles[str(array_key_first($logChannels))->replace(".", "-")->value()] as $logFileName => $logFile)
                                <option value="{{ $logFileName }}" {{ $loop->first ? 'selected' : '' }}>{{ $logFile }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex align-items-center position-relative me-3 my-1">
                        @include("pages.common-components.buttons.download-button", [
                            "id" => "downloadLog",
                            "size" => "",
                            "label" => __("system_log.button.download_log")
                        ])
                    </div>
                </div>
            </div>
            <!--end::Card title--><!--begin::Card toolbar-->
            <div class="card-toolbar mb-0">
                <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x nav-stretch fs-6">
                    @foreach($logChannels as $channel => $channelName)
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#{{ str($channel)->replace(".", "-") }}" id="{{ str($channel)->replace(".", "-") }}-tab">{{ __("system_log.".$channelName) }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <!--end::Card toolbar-->
        </div>

        <!--begin::Card body-->
        <div class="card-body pt-6">
            <div class="tab-content" id="systemLogsTabContent">
                @foreach($logChannels as $channel => $channelName)
                    <div class="tab-pane fade" id="{{ str($channel)->replace(".", "-") }}" role="tabpanel" data-channel="{{ str($channel)->replace(".", "-") }}" data-datatable="{{ $dataTable[$channel]->getTableId() }}">
                        @include('pages.log.system._table', ["table" => $dataTable[$channel]])
                    </div>
                @endforeach
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->

    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function () {
                const files = {!! collect($logFiles) !!};
                pageDetection();
                window.onhashchange = function () {
                    pageDetection();
                }
                $(".nav-link").on("click", function () {
                    window.location.hash = $(this).attr("href").replace("#", "");
                    card = $(this).attr("href").replace("#", "");
                    $($(this).attr("href")).addClass("active");
                });

                var tabElements = [].slice.call(document.querySelectorAll('[data-bs-toggle="tab"]'))
                tabElements.forEach(tabElement => tabElement.addEventListener('shown.bs.tab', function (event) {
                    $("#dtSearch").val("");

                    getActiveDataTable().search("").draw();
                }));

                $("#dtSearch").on("keyup change", function () {
                    getActiveDataTable().search($(this).val()).draw();
                });

                $("#filterFile").select2({
                    placeholder: "{{ __("general.message.please_select") }}",
                    minimumResultsForSearch: -1,
                    width: "100%",
                }).on("change.select2", function (e) {
                    getActiveDataTable().draw();
                });

                $('#downloadLog').click(function() {
                    var file = $("#filterFile").val();
                    var channel = $('#systemLogsTabContent .tab-pane.show').data('channel');

                    // open url
                    const url = "{{ route('system.log.system.download', ['channel' => ':channel', 'file' => ':file']) }}";
                    window.open(url.replace(':channel', channel).replace(':file', file), '_blank');
                })

                function getActiveDataTable() {
                    const datatable = $('#systemLogsTabContent .tab-pane.show').data('datatable');
                    return window.{{ config("datatables-html.namespace", "LaravelDataTables") }}[datatable];
                }

                function pageDetection() {
                    const url = window.location.href.split("#");
                    if(url[1] && url[1].includes("&")) {
                        card = url[1].substring(0, url[1].indexOf("&"));
                        item_id = url[1].substring(url[1].indexOf("&") + 1);
                    } else if(url[1]) {
                        card = url[1];
                        item_id = null;
                    } else {
                        card = $(".nav-link").first().attr("href").replace("#", "");
                        item_id = null;
                    }
                    // $(".tab-pane, .nav-link").removeClass("active");
                    window.location.hash = card;
                    $("#" + card).addClass("active show");
                    $("#" + card + "-tab").addClass("active");

                    const channel = $('#systemLogsTabContent .tab-pane.show').data('channel');
                    $("#filterFile").html("").append($("<option>", {value: "", text: ""})).val("");
                    let first_file = true;
                    $.each(files[channel], function (index, file) {
                        $("#filterFile").append($("<option>", {value: index, text: file, selected: first_file}));
                        first_file = false;
                    });
                }
            });
        </script>
    @endpush

</x-base-layout>
