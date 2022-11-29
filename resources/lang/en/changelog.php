<?php

return [
    // Page Title
    "page_title" => [
        "create" => "New Changelog",
        "edit" => "Edit Changelog",
        "index" => "Changelog"
    ],

    // Message Collection
    "button" => [
        "view_listing" => "View Changelog Listing",
    ],
    "form_label" => [
        "description" => "Description",
        "released_by" => "Release By",
        "released_at" => "Release Date",
        "version" => "Version"
    ],
    "message" => [
        "confirmation_prompt" => "Confirmation",
        "confirmation_prompt_msg" => "Once saved, the version and release information (released by and released at) cannot be changed.",
        "fail_create" => "Failed to create version.",
        "fail_update" => "Failed to update version.",
        "patch" => "patch",
        "patches" => "patches",
        "released_by" => "By :released_by",
        "remove_description_error_msg" => "At least one description is required.",
        "success_create" => "New version successfully created.",
        "success_update" => "Version successfully updated.",
        "version_format_msg" => "
            <span>Version History Format:</span><br/><br/>
            <span>X1.X2.X3, for example, v1.2.1</span><br/>
            <ul>
                <li>X1 reserve for a major update.</li>
                <li>X2 reserve for incremental update/release in a major update.</li>
                <li>X3 reserve for minor change and bug fix.</li>
            </ul>
        ",
        "version_released" => "Version :version, released at :released_at",
        "version_released_without_date" => "Version :version",
    ],
    "validation_label" => [
        "description" => "description",
        "released_by" => "release by",
        "released_at" => "release date",
        "version" => "version"
    ],
];
