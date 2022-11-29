<x-base-layout>
    <x-slot name="page_title_slot">{{ __("changelog.page_title.index") }}</x-slot>

    <!--begin::Card-->
    <div class="card card-flush">
        <div class="card-header pt-6">
            <!--begin::Card title-->
            <div class="card-title">
            </div>
            <!--end::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar gap-3">
                @can("create", \App\Models\Changelog::class)
                    @include("pages.common-components.buttons.add-button", [
                        "link" => route("system.log.changelog.create")
                    ])
                @endcan
            </div>
            <!--end::Card toolbar-->
        </div>
        <div class="card-body">
            <div class="timeline timeline-3">
                @foreach($changelogs as $main_version => $changelog)
                    <div class="timeline-item">
                        <!--begin::Timeline line-->
                        <div class="timeline-line w-40px"></div>
                        <!--end::Timeline line-->
                        <!--begin::Timeline icon-->
                        <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                            <div class="symbol-label bg-light">
                                @if($changelog["main"]->type == "bugfix")
                                    <i class="fa fa-wrench text-danger fs-3"></i>
                                @elseif($changelog["main"]->type == "feature")
                                    <i class="fa fa-arrow-alt-circle-up text-success fs-3"></i>
                                @else
                                    <i class="fa fa-clock text-muted fs-3"></i>
                                @endif
                            </div>
                        </div>
                        <!--end::Timeline icon-->
                        <div class="timeline-content mb-10 mt-n1 border rounded-3 position-relative px-6 py-3">
                            <div class="d-flex justify-content-between">
                                <div class="pe-3 mb-5">
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">{{ isset($changelog["main"]->released_at) ? __("changelog.message.version_released", ["version" => $changelog["main"]->version, "released_at" => $changelog["main"]->released_at]) : __("changelog.message.version_released_without_date", ["version" => $changelog["main"]->version]) }}</div>
                                    <!--end::Title-->
                                    <!--begin::Description-->
                                    <div class="d-flex align-items-center mt-1 fs-6">
                                        <!--begin::Info-->
                                        <div class="text-muted me-2 fs-7">{{ isset($changelog["main"]->released_by) ? __("changelog.message.released_by", ["released_by" => $changelog["main"]->released_by]) : "" }}</div>
                                        <!--end::Info-->
                                    </div>
                                    <!--end::Description-->
                                </div>
                                @if(isset($changelog["main"]->id))
                                <div>
                                    @include("pages.common-components.buttons.hover-buttons.hover-button", [
                                        "color" => "btn-custom-secondary btn-active-custom-light",
                                        "size" => "btn-sm btn-icon",
                                        "attributes" => "data-kt-menu-trigger=click data-kt-menu-placement=bottom-end",
                                        "icon" => "fs-3 la la-ellipsis-h",
                                        "icon_only" => true
                                    ])
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-200px py-4" data-kt-menu="true">
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            @include("pages.common-components.buttons.hover-buttons.hover-link-button", [
                                                "link" => route("system.log.changelog.edit", ["changelog" => $changelog["main"]->id]),
                                                "size" => "btn-sm w-100",
                                                "classes" => "text-start",
                                                "disabled" => \Illuminate\Support\Facades\Auth::user()->cannotUpdate(\App\Models\Changelog::class),
                                                "label" => __("general.button.edit"),
                                                "icon" => null
                                            ])
                                        </div>
                                        <!--end::Menu item-->
                                    </div>
                                </div>
                                @endif
                            </div>
                            @if(isset($changelog["main"]->description) && !empty($changelog["main"]->description))
                                <ul class="mt-3">
                                    @foreach(explode("\n", $changelog["main"]->description) as $description)
                                        <li>{{ $description }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if(count($changelog["patch"]) > 0)
                            <!--begin::Accordion-->
                                <div class="accordion accordion-icon-toggle" id="patches_accordion">
                                    <!--begin::Item-->
                                    <div class="mb-5">
                                        <!--begin::Header-->
                                        <div class="accordion-header py-3 d-flex collapsed" data-bs-toggle="collapse" data-bs-target="#patches_items_{{ str_replace(".", "_", $changelog["main"]->main_version) }}">
                                            <span class="accordion-icon">
                                                {!! theme()->getSvgIcon("icons/duotune/arrows/arr064.svg", "svg-icon svg-icon-4 text-dark") !!}
                                            </span>
                                            <a href="#" class="fw-bold mb-0 ms-4">{{ count($changelog["patch"]) . " " . (count($changelog["patch"]) == 1 ? __("changelog.message.patch") : __("changelog.message.patches")) }}</a>
                                        </div>
                                        <!--end::Header-->
                                        <!--begin::Body-->
                                        <div id="patches_items_{{ str_replace(".", "_", $changelog["main"]->main_version) }}" class="fs-6 collapse ps-10" data-bs-parent="#patches_accordion">
                                            <div class="timeline-label mt-3">
                                                @foreach ($changelog["patch"] as $patch)
                                                <!--begin::Item-->
                                                <div class="timeline-item">
                                                    <!--begin::Label-->
                                                    <div class="timeline-label fw-bolder text-gray-800 fs-6">{{ $patch->version }}</div>
                                                    <!--end::Label-->
                                                    <!--begin::Badge-->
                                                    <div class="timeline-badge">
                                                        {{--<i class="fa fa-genderless text-warning fs-1"></i>--}}
                                                        @if($patch->type == "bugfix")
                                                            <i class="fa fa-wrench text-danger fs-3"></i>
                                                        @elseif($patch->type == "feature")
                                                            <i class="fa fa-arrow-alt-circle-up text-success fs-3"></i>
                                                        @else
                                                            <i class="fa fa-clock text-muted fs-3"></i>
                                                        @endif
                                                    </div>
                                                    <!--end::Badge-->
                                                    <!--begin::Text-->
                                                    <div class="fw-mormal timeline-content ps-3">
                                                        <div>{{ $patch->released_at }}</div>
                                                        <div class="text-muted">{{ $patch->released_by }}</div>

                                                        @if(isset($patch->description) && !empty($patch->description))
                                                            <ul class="mt-3">
                                                                @foreach(explode("\n", $patch->description) as $patch_description)
                                                                    <li>{{ $patch_description }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                    <!--end::Text-->
                                                    <!--begin::Action-->
                                                    <div>
                                                        @include("pages.common-components.buttons.hover-buttons.hover-button", [
                                                            "color" => "btn-custom-secondary btn-active-custom-light",
                                                            "size" => "btn-sm btn-icon",
                                                            "attributes" => "data-kt-menu-trigger=click data-kt-menu-placement=bottom-end",
                                                            "icon" => "fs-3 la la-ellipsis-h",
                                                            "icon_only" => true
                                                        ])
                                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-200px py-4" data-kt-menu="true">
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                @include("pages.common-components.buttons.hover-buttons.hover-link-button", [
                                                                    "link" => route("system.log.changelog.edit", ["changelog" => $patch->id]),
                                                                    "size" => "btn-sm w-100",
                                                                    "classes" => "text-start",
                                                                    "disabled" => \Illuminate\Support\Facades\Auth::user()->cannotUpdate(\App\Models\Changelog::class),
                                                                    "label" => __("general.button.edit"),
                                                                    "icon" => null
                                                                ])
                                                            </div>
                                                            <!--end::Menu item-->
                                                        </div>
                                                    </div>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Item-->
                                                @endforeach
                                            </div>
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::Item-->
                                </div>
                                <!--end::Accordion-->
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!--end::Card-->
</x-base-layout>
