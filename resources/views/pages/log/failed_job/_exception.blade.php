{{ $heading }}
@if($content != "")
    <br/>
    @include("pages.common-components.buttons.hover-buttons.hover-button", [
        "color" => "",
        "size" => "btn-sm btn-link",
        "classes" => "float-right expand viewStack",
        "attributes" => "data-display=stack$id",
        "label" => __("failed_job_log.button.view_more")
    ])
    <br/>
    <div class="stack stack{{ $id }}" style="display: none; white-space: pre-wrap;">{{ $content }}
    </div>
@endif

