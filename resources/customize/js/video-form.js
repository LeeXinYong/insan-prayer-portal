// jshint ignore:start
/**
 *
 * @param selectors
 * @param thumbnail_switch_label_after_fetched
 * @param [selectors.video_type_selector = "#video_type"]
 * @param [selectors.video_section_selector.youtube = "#youtube_section"]
 * @param [selectors.video_section_selector.upload = "#upload_section"]
 * @param [selectors.video_section_selector.video = "#video_section"]
 * @param [selectors.video_input_selector.required.youtube.url = "#youtube_url"]
 * @param [selectors.video_input_selector.required.upload.file_input = "#video_file"]
 * @param [selectors.video_input_selector.hidden.youtube.id = "#youtube_video_id"]
 * @param [selectors.video_input_selector.hidden.youtube.thumbnail_link = "#youtube_thumbnail_link"]
 * @param [selectors.thumbnail_section_selector = "#thumbnail_section"]
 * @param [selectors.thumbnail_switch_selector = "#thumbnail_switch"]
 * @param [selectors.thumbnail_switch_label_selector = "#thumbnail_switch_label"]
 * @param [selectors.manual_thumbnail_selector.section = "#manual_thumbnail_section"]
 * @param [selectors.manual_thumbnail_selector.thumbnail_input = "#manual_thumbnail"]
 * @param [selectors.auto_thumbnail_selector.section = "#auto_thumbnail_section"]
 * @param [selectors.auto_thumbnail_selector.thumbnail_input = "#auto_thumbnail"]
 * @param [selectors.auto_thumbnail_selector.thumbnail_preview = "#thumbnail_preview"]
 * @param [selectors.auto_thumbnail_selector.video_time_slider_section = "#video_time_slider"]
 * @param [selectors.auto_thumbnail_selector.video_slider = "#video_slider"]
 * @param [selectors.auto_thumbnail_selector.video_slider_input = "#video_time"]
 * @param [selectors.auto_thumbnail_selector.current_thumbnail = "#current_thumbnail"]
 * @param [selectors.auto_thumbnail_selector.new_video_upload = "#new_video_upload"]
 * @param [selectors.duration_selector = "#duration"]
 * @param [selectors.video_label = "#video_label"]
 */
function initVideoForm(
    selectors = {},
    thumbnail_switch_label_after_fetched
) {
    const default_selectors = {
        video_type_selector: "#video_type",
        video_section_selector: {
            youtube: "#youtube_section",
            upload: "#upload_section",
            video: "#video_section"
        },
        video_input_selector: {
            required: {
                youtube: {
                    url: "#youtube_url"
                },
                upload: {
                    file_input: "#video_file"
                }
            },
            hidden: {
                youtube: {
                    id: "#youtube_video_id",
                    thumbnail_link: "#youtube_thumbnail_link"
                }
            }
        },
        thumbnail_section_selector: "#thumbnail_section",
        thumbnail_switch_selector: "#thumbnail_switch",
        thumbnail_switch_label_selector: "#thumbnail_switch_label",
        manual_thumbnail_selector: {
            section: "#manual_thumbnail_section",
            thumbnail_input: "#manual_thumbnail",
        },
        auto_thumbnail_selector: {
            section: "#auto_thumbnail_section",
            thumbnail_input: "#auto_thumbnail",
            thumbnail_preview: "#thumbnail_preview",
            video_time_slider_section: "#video_time_slider",
            video_slider: "#video_slider",
            video_slider_input: "#video_time",

            // Edit page only
            current_thumbnail: "#current_thumbnail",
            new_video_upload: "#new_video_upload",
        },
        duration_selector: "#duration",
        video_label: "#video_label",
    }

    selectors = mergeObject(default_selectors, selectors);

    $(selectors.video_type_selector).on("change", function() {
        const video_type = $(this).val();

        if (($($(this).prop("form")).attr("id").split("_")[0] ?? "") === "create") {
            $($(this).prop("form")).trigger("reset");
            $.each(selectors.video_input_selector.hidden, function(type, input_selectors) {
                $.each(input_selectors, function(index, selector) {
                    $(selector).val("");
                });
            });
            $(selectors.auto_thumbnail_selector.thumbnail_input).val("");
            $(selectors.auto_thumbnail_selector.thumbnail_preview).attr("src", "");

            $(selectors.thumbnail_section_selector).hide();
            $(selectors.thumbnail_switch_selector).prop("checked", false).trigger("change");
            $(selectors.video_section_selector.video).addClass("col-12").removeClass("col-6");
            $(selectors.video_label).removeClass("h-60px h-sm-45px");

            // restore back video type
            $(this).val(video_type);
        }

        $(selectors.video_section_selector[video_type]).show();
        $.each(selectors.video_section_selector, function(type, selector) {
            if(type === "video") {
            } else if(type !== video_type) {
                $(selector).hide();
            }
        });
        $.each(selectors.video_input_selector.required, function(type, input_selectors) {
            $.each(input_selectors, function(index, selector) {
                if(type === video_type) {
                    $(selector).prop("required", true);
                } else {
                    $(selector).prop("required", false);
                }
            });
        });
    });

    $(selectors.thumbnail_switch_selector).on("change", function() {
        if($(this).is(":checked")) {
            if (($($(this).prop("form")).attr("id").split("_")[0] ?? "") === "edit") {
                if($(selectors.auto_thumbnail_selector.new_video_upload).val() === "0") {
                    $(selectors.thumbnail_section_selector).hide();
                    $(selectors.auto_thumbnail_selector.current_thumbnail).show();
                }
            }
            $(selectors.manual_thumbnail_selector.section).hide();
            $(selectors.manual_thumbnail_selector.thumbnail_input).prop("required", false);
            $(selectors.auto_thumbnail_selector.section).show();
        } else {
            if (($($(this).prop("form")).attr("id").split("_")[0] ?? "") === "edit") {
                $(selectors.auto_thumbnail_selector.current_thumbnail).hide();
                $(selectors.thumbnail_section_selector).show();
            }
            $(selectors.auto_thumbnail_selector.section).hide();
            $(selectors.manual_thumbnail_selector.section).show();
            $(selectors.manual_thumbnail_selector.thumbnail_input).prop("required", true);
        }
    });

    if(selectors.video_input_selector.required.upload.file_input !== undefined) {
        $(selectors.video_input_selector.required.upload.file_input).on("change", function() {
            const video_input = $(this);
            const file = video_input.prop("files")[0];
            if(file !== undefined && isVideo(file)) {
                // Show spinner
                SpinnerSingletonFactory.block();

                let video = document.createElement("video");
                video.src = URL.createObjectURL(file);
                let frame = [];
                let canvas = document.createElement("canvas");
                let context = canvas.getContext("2d");

                let start_time = 1;
                let max_duration = 15;

                let max_width = 600;
                let max_height = 600;

                let interval = 1/2;

                function initSlider() {
                    const slider = $(selectors.auto_thumbnail_selector.video_slider)[0];
                    const slider_input = $(selectors.auto_thumbnail_selector.video_slider_input)[0];
                    if(slider.noUiSlider){
                        slider.noUiSlider.destroy();
                    }
                    noUiSlider.create(slider, {
                        start: [ start_time ],
                        range: {
                            "min": 0,
                            "max": max_duration
                        },
                        format: {
                            to: function (value) {
                                return secondsToTime(value, true);
                            },
                            from: timeToSeconds
                        },
                        step: 1
                    });

                    slider.noUiSlider.on("update", function( values, handle ) {
                        slider_input.value = values[handle];
                        drawOnCanvas(timeToSeconds(values[handle]));
                    });

                    slider_input.addEventListener("change", function(){
                        slider.noUiSlider.set(video_input.val());
                        drawOnCanvas(timeToSeconds(video_input.val()));
                    });
                }

                function fit(width, height) {
                    let widthAmp = width / max_width;
                    let heightAmp = height / max_height;
                    if (widthAmp > 1 && widthAmp >= heightAmp) {
                        return [width / widthAmp, height / widthAmp];
                    } else if (heightAmp > 1 && heightAmp >= widthAmp) {
                        return [width / heightAmp, height / heightAmp];
                    } else {
                        return [width, height];
                    }
                }

                function initCanvas() {
                    let size = fit(video.videoWidth ?? "", video.videoHeight ?? "");
                    canvas.width = size[0];
                    canvas.height = size[1];
                }

                function drawOnCanvas(time) {
                    let thumbnail = $(selectors.auto_thumbnail_selector.thumbnail_preview);
                    thumbnail.on("load", function() {
                        URL.revokeObjectURL($(this).attr("src"));
                    });
                    let index = Math.floor(time/max_duration * frame.length) - 1;
                    let blob = frame[(index < 0 ? 0 : index)];
                    thumbnail.attr("src", URL.createObjectURL(blob));
                    blobToDataURL(blob, function (data_url) {
                        $(selectors.auto_thumbnail_selector.thumbnail_input).val(data_url);
                    });

                    if (($(video_input.prop("form")).attr("id").split("_")[0] ?? "") === "edit") {
                        $(selectors.auto_thumbnail_selector.new_video_upload).val("1");
                        $(selectors.auto_thumbnail_selector.current_thumbnail).hide();
                    } else {
                        $(selectors.thumbnail_switch_selector).prop("checked", true).trigger("change");
                    }

                    $(selectors.auto_thumbnail_selector.video_time_slider_section).show();
                    $(selectors.thumbnail_section_selector).fadeIn("slow");

                    // Hide spinner
                    SpinnerSingletonFactory.unblock();

                    // we don't need the video's objectURL anymore
                    URL.revokeObjectURL(video.src);
                }

                function getCanvasBlob(canvas) {
                    return new Promise(function(resolve) {
                        canvas.toBlob(function(blob) {
                            resolve(blob);
                        }, "image/png");
                    });
                }

                function blobToDataURL(blob, callback) {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        callback(e.target.result);
                    }
                    reader.readAsDataURL(blob);
                }

                function drawFrame() {
                    if (video.currentTime < max_duration) {
                        context.drawImage(video, 0, 0, canvas.width, canvas.height);
                        getCanvasBlob(canvas).then(function (blob) {
                            saveFrame(blob);
                            video.currentTime += interval;
                        });
                    } else {
                        video.dispatchEvent(new Event("loadended"));
                        video.removeEventListener("loadended", initSlider);
                    }
                }

                function saveFrame(blob) {
                    frame.push(blob);
                }

                function onend() {
                    let thumbnail = $(selectors.auto_thumbnail_selector.thumbnail_preview);
                    thumbnail.on("load", function () {
                        URL.revokeObjectURL($(this).attr("src"));
                    });
                    let blob = frame[Math.floor(start_time / max_duration * frame.length) - 1];
                    thumbnail.attr("src", URL.createObjectURL(blob));
                    blobToDataURL(blob, function (data_url) {
                        $(selectors.auto_thumbnail_selector.thumbnail_input).val(data_url);
                    });
                    if (($(video_input.prop("form")).attr("id").split("_")[0] ?? "") === "create") {
                        $(selectors.thumbnail_section_selector).addClass("col-6").removeClass("col-12");
                    }
                    $(selectors.video_section_selector.video).addClass("col-6").removeClass("col-12");
                    $(selectors.video_label).addClass("h-60px h-sm-45px");
                    $(selectors.auto_thumbnail_selector.video_time_slider_section).show();
                    $(selectors.thumbnail_section_selector).fadeIn("slow");

                    // Hide spinner
                    SpinnerSingletonFactory.unblock();

                    // we don't need the video's objectURL anymore
                    URL.revokeObjectURL(video.src);
                }

                video.muted = true;
                video.addEventListener("loadedmetadata", initCanvas, false);
                video.addEventListener("seeked", drawFrame, false);
                video.addEventListener("loadended", initSlider, false);
                video.addEventListener("loadended", onend, false);

                // get video duration
                video.onloadedmetadata = function() {
                    $(selectors.duration_selector).val(secondsToTime(video.duration));
                };

                // load video
                video.onloadeddata = function(){
                    //  update max duration if video duration smaller than default (15 seconds)
                    max_duration = Math.min(max_duration, video.duration);

                    drawFrame();
                };

                if (($(video_input.prop("form")).attr("id").split("_")[0] ?? "") === "edit") {
                    $(selectors.auto_thumbnail_selector.new_video_upload).val("1");
                    $(selectors.auto_thumbnail_selector.current_thumbnail).hide();
                }
                $(selectors.thumbnail_switch_label_selector).html(thumbnail_switch_label_after_fetched);
            }
        })
    }
}

function isVideo(file){
    return ["mp4"].includes(file.name.split(".").pop())
}

function secondsToTime(value, ms = false) {
    let totalSeconds = value;
    let hours = Math.floor(totalSeconds / 3600);
    totalSeconds %= 3600;
    const minutes = Math.floor(totalSeconds / 60).toString().padStart(2, "0");
    const seconds = Math.floor(totalSeconds % 60).toString().padStart(2, "0");
    const milliseconds = (totalSeconds - Math.floor(totalSeconds)).toFixed(2).toString().split(".")[1];
    return ((hours > 0) ? hours : "") + minutes + ":" + seconds + (ms ? "." + milliseconds : "");
}

function timeToSeconds(value) {
    const split = value.toString().split(":");
    let hour, min, seconds;
    hour = parseInt(split[split.length - 3] ?? 0, 10) * 3600;
    min = parseInt(split[split.length - 2] ?? 0, 10) * 60;
    seconds = parseFloat(split[split.length - 1] ?? 0);
    return hour + min + seconds;
}
// jshint ignore:end
