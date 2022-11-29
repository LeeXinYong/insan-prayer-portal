@php
    $breadcrumb = bootstrap()->getBreadcrumb();
    $backlink = null;

    $temp = $breadcrumb;
    array_pop($temp);
    while(!empty($temp)) {
        $last_breadcrumb = array_pop($temp);
        if(isset($last_breadcrumb['path']) && $last_breadcrumb['path'] != "/" && $last_breadcrumb['path'] != "") {
            $backlink = theme()->getPageUrl($last_breadcrumb['path']);
            break;
        }
    }
@endphp

@if ( theme()->getOption('layout', 'page-title/back-button') && !empty($breadcrumb) )
    @if(isset($backlink) && $backlink != null)
        <div class="d-flex align-items-center">
            @include("pages.common-components.buttons.hover-buttons.hover-link-button", [
                "color" => "",
                "link" => $backlink,
                "size" => "btn-link",
                "classes" => "text-dark fw-normal mt-n6 mt-sm-n10",
                "icon" => "fs-2x ra-back",
                "label" => __("general.button.back")
            ])
        </div>
    @endif
@endif
