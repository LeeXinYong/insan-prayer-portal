// jshint ignore:start
function initImageFileInput(selector, drop_zone_title, drop_zone_click_title, image_extension = ["jpeg", "jpg", "png"], max_image_size = 2000, max_image_width = null, max_image_height = null, initial_preview = [], initial_preview_config = [], options = {}) {
    $(selector).fileinput({
        theme: "fas",
        allowedFileExtensions: image_extension, // set allowed file format
        maxFileSize: max_image_size, //set file size limit, 1000 = 1MB
        autoOrientImage: false,
        showUpload: false,
        showCancel: false,
        showRemove: true,
        browseOnZoneClick: true,
        dropZoneTitle: drop_zone_title,
        dropZoneClickTitle: drop_zone_click_title,
        layoutTemplates: {
            actionDelete: "",
            actionDrag: "",
        },
        initialPreview: initial_preview,
        initialPreviewAsData: initial_preview !== [],
        initialPreviewConfig: initial_preview_config,
        overwriteInitial: initial_preview !== [],
        allowedPreviewMimeTypes: image_extension,
        previewFileIcon: '<i class="fas fa-file fs-fluid"></i>',
        previewFileIconSettings: {
            "docx": '<i class="fas fa-file-word text-primary fs-fluid"></i>',
            "jpeg": '<i class="fas fa-file-image text-warning fs-fluid"></i>',
            "jpg": '<i class="fas fa-file-image text-warning fs-fluid"></i>',
            "mp4": '<i class="fas fa-file-video text-muted fs-fluid"></i>',
            "pdf": '<i class="fas fa-file-pdf text-danger fs-fluid"></i>',
            "png": '<i class="fas fa-file-image text-warning fs-fluid"></i>',
            "pptx": '<i class="fas fa-file-powerpoint text-danger fs-fluid"></i>',
            "xlsx": '<i class="fas fa-file-excel text-success fs-fluid"></i>',
            "zip": '<i class="fas fa-file-archive text-muted fs-fluid"></i>',
        },
        maxImageWidth: max_image_width,
        maxImageHeight: max_image_height,
        minImageWidth: max_image_width,
        minImageHeight: max_image_height,
        ...options,
    });
}

/**
 *
 * @param selector
 * @param options.allowedFileExtensions
 * @param options.maxFileSize
 * @param options.dropZoneTitle
 * @param options.dropZoneClickTitle
 * @param options.initialPreview
 * @param options.initialPreviewAsData
 * @param options.initialPreviewConfig
 * @param options.overwriteInitial
 * @param options.allowedPreviewMimeTypes
 * @param options.maxImageWidth
 * @param options.maxImageHeight
 * @param options.minImageWidth
 * @param options.minImageHeight
 */
function initImageFileInputRaw(selector, options = {}) {
    const fileInputOptions = {
        theme: "fas",
        autoOrientImage: false,
        showUpload: false,
        showCancel: false,
        showRemove: true,
        browseOnZoneClick: true,
        layoutTemplates: {
            actionDelete: "",
            actionDrag: "",
        },
        previewFileIcon: '<i class="fas fa-file fs-fluid"></i>',
        previewFileIconSettings: {
            "docx": '<i class="fas fa-file-word text-primary fs-fluid"></i>',
            "jpeg": '<i class="fas fa-file-image text-warning fs-fluid"></i>',
            "jpg": '<i class="fas fa-file-image text-warning fs-fluid"></i>',
            "mp4": '<i class="fas fa-file-video text-muted fs-fluid"></i>',
            "pdf": '<i class="fas fa-file-pdf text-danger fs-fluid"></i>',
            "png": '<i class="fas fa-file-image text-warning fs-fluid"></i>',
            "pptx": '<i class="fas fa-file-powerpoint text-danger fs-fluid"></i>',
            "xlsx": '<i class="fas fa-file-excel text-success fs-fluid"></i>',
            "zip": '<i class="fas fa-file-archive text-muted fs-fluid"></i>',
        },
        ...options,
    };

    $(selector).fileinput(fileInputOptions);
}

function initFileInput(selector, drop_zone_title, drop_zone_click_title, file_extension = ["pdf"], max_file_size = 2000, initial_preview = [], initial_preview_config = [], options = {}) {
    $(selector).fileinput({
        theme: "fas",
        allowedFileExtensions: file_extension, // set allowed file format
        maxFileSize: max_file_size, //set file size limit, 1000 = 1MB
        autoOrientImage: false,
        showUpload: false,
        showCancel: false,
        showRemove: true,
        browseOnZoneClick: true,
        dropZoneTitle: drop_zone_title,
        dropZoneClickTitle: drop_zone_click_title,
        layoutTemplates: {
            actionDelete: "",
            actionDrag: "",
        },
        initialPreview: initial_preview,
        initialPreviewAsData: initial_preview !== [],
        initialPreviewConfig: initial_preview_config,
        overwriteInitial: initial_preview !== [],
        allowedPreviewMimeTypes: file_extension,
        previewFileIcon: '<i class="fas fa-file fs-fluid"></i>',
        previewFileIconSettings: {
            "docx": '<i class="fas fa-file-word text-primary fs-fluid"></i>',
            "jpeg": '<i class="fas fa-file-image text-warning fs-fluid"></i>',
            "jpg": '<i class="fas fa-file-image text-warning fs-fluid"></i>',
            "mp4": '<i class="fas fa-file-video text-muted fs-fluid"></i>',
            "pdf": '<i class="fas fa-file-pdf text-danger fs-fluid"></i>',
            "png": '<i class="fas fa-file-image text-warning fs-fluid"></i>',
            "pptx": '<i class="fas fa-file-powerpoint text-danger fs-fluid"></i>',
            "xlsx": '<i class="fas fa-file-excel text-success fs-fluid"></i>',
            "zip": '<i class="fas fa-file-archive text-muted fs-fluid"></i>',
        },
        ...options,
    });
}

function initImageInput(selector, image_extension = ["jpeg", "jpg", "png"], max_image_size = 2048)
{
    let limit = {
        maxFileSize: max_image_size,
        allowedFileExtensions: image_extension
    };
    let inputElement = document.querySelector(selector);
    let imageInputElement = document.querySelector(selector + "_image_input");
    let imageInputInstance = KTImageInput.getInstance(imageInputElement);
    let imageInputWrapperElement = document.querySelector(selector + "_image_input .image-input-wrapper");
    let errorMessageElement = document.querySelector(selector + "_error_message");

    let message = [];
    message["size_too_large"] = "File \":name\" (<strong>:size KB</strong>) exceeds maximum allowed upload size of <strong>:max_size KB</strong>.";
    message["invalid_extension"] = "Invalid extension for file \":name\". Only \":extension\" files are supported.";

    let message_element_template =
        `<div class="alert bg-light-danger alert-dismissible d-flex align-items-center p-5 mb-0 mt-5 border-danger" role="alert">
            <!--begin::Wrapper-->
            <div class="d-flex flex-column pe-0 pe-sm-10 fs-6 text-danger">
                <!--begin::Content-->
                <span class="text-center">:message_content</span>
                <!--end::Content-->
            </div>
            <!--end::Wrapper-->

            <!--begin::Close-->
            <button type="button" class="position-relative m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto justify-content-end" data-bs-dismiss="alert">
                <i class="la la-close text-dark"></i>
            </button>
            <!--end::Close-->
        </div>`;

    imageInputInstance.on("kt.imageinput.change", function() {
        // Clear error message
        errorMessageElement.innerHTML = "";

        // Get uploaded file
        const file = inputElement.files[0];

        // Check uploaded file size
        if(limit.maxFileSize !== null && file.size > (limit.maxFileSize * 1024)) {
            // Display error message
            errorMessageElement.innerHTML = message_element_template.replace(":message_content", message["size_too_large"]
                .replace(":name", file.name)
                .replace(":size", (file.size / 1025).toFixed(2).toString())
                .replace(":max_size", limit.maxFileSize.toString())
            );

            // Clear image preview
            imageInputElement.classList.replace("image-input-changed", "image-input-empty");
            imageInputWrapperElement.style.backgroundImage = "";

            // Clear file input
            inputElement.value = "";

            // Stop input
            return false;
        }

        // Check uploaded file extension
        if(limit.allowedFileExtensions !== null && Array.isArray(limit.allowedFileExtensions) && !limit.allowedFileExtensions.includes(file.name.split('.').pop())) {
            // Display error message
            errorMessageElement.innerHTML = message_element_template.replace(":message_content", message["invalid_extension"]
                .replace(":name", file.name)
                .replace(":extension", limit.allowedFileExtensions.join(", "))
            );

            // Clear image preview
            imageInputElement.classList.replace("image-input-changed", "image-input-empty");
            imageInputWrapperElement.style.backgroundImage = "";

            // Clear file input
            inputElement.value = "";

            // Stop input
            return false;
        }
    });
}
// jshint ignore:end
