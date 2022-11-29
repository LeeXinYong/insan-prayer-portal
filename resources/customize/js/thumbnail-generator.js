// jshint ignore:start
$("#thumbnail_switch").on("click", function () {
    if($(this).prop("checked")) {
        if (($($("#thumbnail_switch").prop("form")).attr("id").split("_")[0] ?? "") === "edit") {
            if ($("#new_upload").val() === "0") {
                $("#thumbnail").hide();
                $("#current_thumbnail").show();
            }
        }

        $("#manual_thumbnail_section").hide();
        $("#manual_thumbnail").prop("required", false);
        $("#auto_thumbnail_section").show();
        setCropper("cropper_preview", document.getElementById("the-canvas").width, document.getElementById("the-canvas").height);
    } else {
        if (($($("#thumbnail_switch").prop("form")).attr("id").split("_")[0] ?? "") === "edit") {
            $("#current_thumbnail").hide();
            $("#thumbnail").show();
        }

        $("#auto_thumbnail_section").hide();
        $("#manual_thumbnail_section").show();
        $("#manual_thumbnail").prop("required", true);
    }
});

// GENERATE THUMBNAIL FROM PDF
//
// Disable workers to avoid yet another cross-origin issue (workers need the URL of
// the script to be loaded, and dynamically loading a cross-origin script does
// not work)
//
pdfjsLib.disableWorker = true;
//
// Asynchronous download PDF as an ArrayBuffer
//
let pdf = document.getElementById("pdf_file");
pdf.onchange = function (ev) {
    const file = document.getElementById("pdf_file").files[0];
    if (file) {
        SpinnerSingletonFactory.block();

        let fileReader = new FileReader();
        fileReader.onload = function (ev) {
            pdfjsLib.getDocument(fileReader.result).then(function getPdfHelloWorld(pdffile) {
                //
                // Fetch the first page
                //
                pdffile.getPage(1).then(function getPageHelloWorld(page) {
                    var scale = 1;
                    var viewport = page.getViewport(scale);
                    //
                    // Prepare canvas using PDF page dimensions
                    //
                    var canvas = document.getElementById("the-canvas");
                    var context = canvas.getContext("2d");
                    var thumbHeight = canvas.height;
                    var thumbWidth = canvas.width;
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    //
                    // Render PDF page into canvas context
                    //
                    var task = page.render({
                        canvasContext: context,
                        viewport: viewport
                    })
                    task.promise.then(function () {
                        if (($($("#pdf_file").prop("form")).attr("id").split("_")[0] ?? "") === "edit") {
                            $("#new_upload").val("1");
                            $("#current_thumbnail").hide();
                        }

                        $("#file_upload_section").removeClass('col-12').addClass('col-6');
                        $("#pdf_file_label").addClass("h-60px h-sm-45px");
                        $("#thumbnail").fadeIn("slow");
                        // cropper
                        $("#cropper_preview").attr("src", canvas.toDataURL("image/jpeg"));
                        setCropper("cropper_preview", thumbWidth, thumbHeight);
                        $("#auto_thumbnail").val(canvas.toDataURL("image/jpeg"));
                        SpinnerSingletonFactory.unblock();
                    });
                });
            }, function (error) {
                if(error.code === 1) {
                    $("#pdffile").val("")
                    $("#pdffile-input div.kv-fileinput-error").html('<button type="button" onclick="closeError(\'#pdffile-input div.kv-fileinput-error\')" class="btn-close kv-error-close float-end" aria-label="Close"></button>"pdf" files with password encrypted ae not supported.').show()
                    $("#pdffile-input input.kv-fileinput-caption").removeClass("is-valid").addClass("is-invalid").val("No files selected");
                    $("#pdffile-input span.file-caption-icon").html('<span class="text-danger"><i class="bi-exclamation-circle-fill"></i> </span>');
                    $("#pdffile-input div.file-upload-indicator").html('<i class="bi-exclamation-lg text-danger"></i>');
                } else {
                    console.log(error);
                }
                SpinnerSingletonFactory.unblock();
            });
        };
        fileReader.readAsArrayBuffer(file);
    }
}

let cropper = "";

function setCropper(name, width, height) {
    const image = document.getElementById(name);
    if (cropper !== "") {
        cropper.destroy();
    }
    cropper = new Cropper(image, {
        aspectRatio: width / height,
        viewMode: 2,
        dragMode: "move",
        cropBoxResizable: true,
        // minCropBoxWidth: 10,
        // minContainerWidth: 500,
        // minContainerHeight: 500,
        crop: (event) => {
            let ori = document.getElementById("canvas_div");
            ori.innerHTML = "";
            ori.appendChild(cropper.getCroppedCanvas({
                width: width,
                height: height
            }));
            ori.childNodes[0].setAttribute("id", "the-canvas");
            ori.childNodes[0].style.display = "none";
            $("#auto_thumbnail").val(ori.childNodes[0].toDataURL("image/jpeg"));
        },
    });
}

function closeError(element) {
$(element).fadeOut("slow");
}
// jshint ignore:end
