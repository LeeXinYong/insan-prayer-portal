<x-base-layout>
    <x-slot name="page_title_slot">{{ __("user.page_title.view") }}</x-slot>
    <!--begin::Card-->
    <div class="card mb-6">
        <div class="card-body p-9">
            <div class="d-flex flex-wrap flex-sm-nowrap">
                <!--begin: Pic-->
                <div class="me-7 mb-4">
                    <div class="symbol symbol-60px symbol-lg-60px symbol-circle symbol-fixed position-relative shadow-sm border border-3 border-light">
                        <img src="{{ asset(theme()->getCustomizeUrlPath() . "media/avatars/default_user.svg") }}"
                            alt="image"/>
                    </div>
                </div>
                <!--end::Pic-->
                <!--begin::Info-->
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-gray-900 fs-2 fw-bolder me-1" id="status_card_name">{{ $user->name }}</span>
                                <span id="status_badge" class="badge badge-light-{{ $user->status ? "success" : "danger" }} fw-bolder ms-2 fs-8 py-1 px-3">{{ $user->status ? __("general.message.active") : __("general.message.inactive") }}</span>
                            </div>
                            <!--begin::Role-->
                            <div class="d-flex align-items-center mb-2 text-primary fs-6 fw-bold">
                                <div id="roles">
                                    @foreach ($user->roles as $role)
                                        <span>{{ $role->name }}</span>
                                        @if(!$loop->last)
                                            <span class="mx-3">|</span>
                                        @endif
                                    @endforeach
                                </div>

                                <span class="d-flex align-items-center text-gray-400 ms-5 text-truncate">
                                    <i class="fs-3 la la-clock"></i>
                                    &nbsp;{{ __("profile.message.registered_date", ["time" => $user->created_at]) }}
                                </span>
                            </div>
                            <!--end::Role-->
                        </div>
                        <div class="d-flex my-4">
                            @include('pages.user._actions')
                        </div>
                    </div>

                    <!--begin::Info-->
                    <div class="d-flex flex-column flex-grow-1 pt-8 pe-8">
                        <div class="row gy-8">
                            <div class="col-4">
                                <div class="fs-6 text-gray-400">{{ __("user.form_label.email", []) }}</div>
                                <div class="fs-6" id="displayEmail">{{ $user->email }}</div>
                            </div>
                            <div class="col-4 {{ (!empty($user->mobile)) ? "" : "d-none" }}">
                                <div class="fs-6 text-gray-400">{{ __("user.form_label.mobile", []) }}</div>
                                <div class="fs-6" id="displayMobile">{{ $user->mobile }}</div>
                            </div>
                            <div class="col-4">
                                <div class="fs-6 text-gray-400">{{ __("user.view_label.country") . ", " . __("user.view_label.timezone") }}</div>
                                <div class="fs-6" id="displayTimezone">
                                    @if(isset($user->country))
                                        @if(isset($user->country->flag_icon_svg))
                                            {!! theme()->getSvgIcon($user->country->flag_icon_svg, "svg-icon-3 svg-icon-success me-2") !!}
                                        @endif
                                        {{ $user->country->name . ", " . $user->timezoneInfo->timezone_name . " " . $user->timezoneInfo->offset }}
                                    @else
                                        {{ __("general.message.not_applicable") }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="fs-6 text-gray-400">{{ __("user.last_login", []) }}</div>
                                <div class="fs-6">{{ ($user->last_login == 'N/A') ? $user->last_login : $user->last_login_duration . " (" . $user->last_login . ")" }}</div>
                            </div>
                        </div>
                    </div>
                    <!--end::Info-->
                </div>
                <!--end::Info-->
            </div>
        </div>
    </div>
    <!--end::Card-->

    <!--begin::Card-->
    <div class="card mb-6 mt-6 mt-md-0">
        <div class="card-header min-h-60px card-header-stretch" style="overflow-x: auto">
            <!--begin::Navs-->
            @include("pages.user._nav-menu")
            <!--end::Navs-->
        </div>
        <div class="card-body">
            <!--begin::Tab-->
            <div class="tab-content">
                <div class="tab-pane" id="activities">
                    <!--begin::Card-->
                @include("pages.user._overview")
                <!--end::Card-->
                </div>
                <div class="tab-pane" id="permissions">
                    <!--begin::Card-->
                @include("pages.user._permissions")
                <!--end::Card-->
                </div>
            </div>
            <!--end::Tab-->
        </div>
    </div>
    <!--end::Card-->

    @can("update", $user)
        @include('pages.user.edit-user-modal')
    @endcan


    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript">
            let card;
            let item_id;

            $(document).ready(function () {
                pageDetection();
                window.onhashchange = function () {
                    pageDetection();
                }
                $(".nav-link").on("click", function () {
                    window.location.hash = $(this).attr("href").replace("#", "");
                    card = $(this).attr("href").replace("#", "");
                    $($(this).attr("href")).addClass("active");
                });
            });

            function pageDetection() {
                const url = window.location.href.split("#");
                if (url[1] && url[1].includes("&")) {
                    card = url[1].substring(0, url[1].indexOf("&"));
                    item_id = url[1].substring(url[1].indexOf("&") + 1);
                } else if (url[1]) {
                    card = url[1];
                    item_id = null;
                } else {
                    card = $(".nav-link").first().attr("href").replace("#", "");
                    item_id = null;
                }
                $(".tab-pane, .nav-link").removeClass("active");
                window.location.hash = card;
                $("#" + card).addClass("active");
                $("#" + card + "_tab").addClass("active");
            }
        </script>
    @endpush
</x-base-layout>
