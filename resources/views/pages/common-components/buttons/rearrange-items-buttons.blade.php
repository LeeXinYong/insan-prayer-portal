@include("pages.common-components.buttons.arrange-button", [
    "classes" => "rearrange-items",
])
@include("pages.common-components.buttons.cancel-button", [
    "link" => "#",
    "classes" => "btn-outline cancel-rearrange-items d-none"
])
@include("pages.common-components.buttons.save-button", [
    "indicator" => true,
    "classes" => "save-rearrange-items d-none",
])
