@if((count($actions) == 1 && !($forceDropdown ?? false)))
    @foreach($actions as $index => $action)
        @if($action["disabled"] ?? false)
        <div class="cursor-not-allowed" @if($action["disabled_prompt"] ?? false) data-bs-toggle="tooltip" data-bs-placement="left" title="{{ $action["disabled_prompt"] }}" @endif>
        @endif
        @include("pages.common-components.buttons.hover-buttons." . (isset($action["url"]) ? "hover-link-button" : "hover-button"), [
            "id" => $action["id"] ?? null,
            "link" => (isset($action["url"]) && $index != "delete") ? $action["url"] : null,
            "color" => "btn-custom-secondary btn-active-custom-light",
            "size" => "btn-sm",
            "classes" => ($index == "delete" ? "text-danger " : "") .
                ($action["classes"] ?? ""),
            "attributes" =>
                (isset($action["url"]) && $index == "delete" ? ("data-destroy={$action["url"]} ") : "") .
                (isset($action["data"]) && count($action["data"]) > 0 ? implode(" ", array_map(function($key, $value) { return "data-" . $key . "=" . $value; }, array_keys($action["data"]), $action["data"])) : ""),
            "disabled" => $action["disabled"] ?? false,
            "label" => $action["label"],
            "icon" => $action["icon"] ?? null,
        ])
        @if($action["disabled"] ?? false)
        </div>
        @endif
    @endforeach
@else
    @include("pages.common-components.buttons.hover-buttons.hover-button", [
        "color" => "btn-custom-secondary btn-active-custom-light",
        "size" => "btn-sm btn-icon",
        "attributes" => "data-kt-menu-trigger=click data-kt-menu-placement=bottom-end",
        "icon" => "fs-3 la la-ellipsis-h",
        "icon_only" => true
    ])
    <!--begin::Menu-->
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-200px py-4" data-kt-menu="true" style="">
        @foreach($actions as $index => $action)
            <!--begin::Menu item-->
            @if($action["disabled"] ?? false)
            <div class="menu-item px-3 cursor-not-allowed" @if($action["disabled_prompt"] ?? false) data-bs-toggle="tooltip" data-bs-placement="left" title="{{ $action["disabled_prompt"] }}" @endif>
            @else
            <div class="menu-item px-3">
            @endif
            @include("pages.common-components.buttons.hover-buttons." . (isset($action["url"]) ? "hover-link-button" : "hover-button"), [
                "id" => $action["id"] ?? null,
                "link" => (isset($action["url"]) && !in_array($index, ["delete", "retry"])) ? $action["url"] : null,
                "size" => "btn-sm w-100",
                "classes" => "text-start " .
                    ($index == "delete" ? "text-danger " : "") .
                    ($action["classes"] ?? ""),
                "attributes" =>
                    (isset($action["url"]) && $index == "delete" ? ("data-destroy={$action["url"]} ") : "") .
                    (isset($action["url"]) && $index == "retry" ? ("data-retry={$action["url"]} ") : "") .
                    (isset($action["data"]) && count($action["data"]) > 0 ? implode(" ", array_map(function($key, $value) { return "data-" . $key . "=" . $value; }, array_keys($action["data"]), $action["data"])) : ""),
                "disabled" => $action["disabled"] ?? false,
                "label" => $action["label"],
                "icon" => $action["icon"] ?? null,
            ])
            </div>
            <!--end::Menu item-->
        @endforeach
    </div>
    <!--end::Menu-->
@endif
