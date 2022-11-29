<?php
return array(
    //Assets
    'assets' => array(
        'favicon' => 'customize/media/logos/brand-icon.svg',
        'fonts' => array(
            'google' => array(
                'Poppins:300,400,500,600,700',
            ),
        ),
        'css' => array(
            'plugins/global/plugins.bundle.css',
            'plugins/global/plugins-custom.bundle.css',
            'css/style.bundle.css',

            // Override Default Metronic Styling
            'customize/css/override.css',

            'css/redant-font.css',
            'css/app.css',
        ),
        'js' => array(
            'plugins/global/plugins.bundle.js',
            'js/scripts.bundle.js',
            'js/custom/widgets.js',

            // General Custom JS
            'customize/js/spinner-singleton-factory.js',
            'customize/js/SweetAlert-wrapper.js',
            'customize/plugins/jTimeout/jTimeout.min.js',
        ),
    ),

    //Layout
    'layout' => array(
        //Main
        'main' => array(
            'type' => 'default', //Setlayouttype:default|blank|none
            'dark-mode-enabled' => true, //Enableoptioanldarkmodemode
            'primary-color' => '#04C8C8',
        ),

        //Loader
        'loader' => array(
            'display' => false,
            'type' => 'default' //Setdefault|spinner-message|spinner-logotohideorshowpageloader
        ),

        //Scrolltop
        'scrolltop' => array(
            'display' => true //Enablescrolltop
        ),

        //Header
        'header' => array(
            'display' => true, //Settrue|falsetoshoworhideHeader
            'width' => 'fixed', //Setfixed|fluidtochangewidthtype
            'fixed' => array(
                'desktop' => false, //Settrue|falsetosetfixedHeaderfordesktopmode
                'tablet-and-mobile' => false //Settrue|falsetosetfixedHeaderfortabletandmobilemodes
            ),
            'search' => false,
            'activity-stream' => false,
        ),

        //Pagetitle
        'page-title' => array(
            'display' => true, //Displaypagetitle
            'breadcrumb' => true, //Displaybreadcrumb
            'description' => false, //Displaydescription
            'responsive' => true, //Movepagetitletocotnentonmobilemode
            'responsive-breakpoint' => 'lg', //Responsivebreakpointvalue(e.g:md,lg,or300px)
            'responsive-target' => '#kt_toolbar_container' //Responsivetargetselector
        ),

        //Aside
        'aside' => array(
            'menu-icon' => 'svg'//Menuicontype(svg|font)
        ),

        //Sidebar
        'sidebar' => array(
            'display' => false, //Settrue|falsetoshoworhideSidebar
        ),

        //Content
        'content' => array(
            'width' => 'fixed', //Setfixed|fluidtochangewidthtype
        ),

        //Footer
        'footer' => array(
            'width' => 'fixed' //Setfixed|fluidtochangewidthtype
        ),
    ),
);
