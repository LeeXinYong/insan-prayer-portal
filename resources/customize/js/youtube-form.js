// jshint ignore:start
/**
 *
 * @param selectors
 * @param [selectors.button = "#fetch_youtube_data_btn"]
 * @param [selectors.video_section_selector = "#video_section"]
 * @param [selectors.video_url = "#youtube_url"]
 * @param [selectors.video_id = "#youtube_video_id"]
 * @param [selectors.video_title = "#video_title"]
 * @param [selectors.thumbnail_section_selector = "#thumbnail_section"]
 * @param [selectors.thumbnail_switch_selector = "#thumbnail_switch"]
 * @param [selectors.thumbnail_switch_label_selector = "#thumbnail_switch_label"]
 * @param [selectors.manual_thumbnail_selector.section = "#manual_thumbnail_section"]
 * @param [selectors.manual_thumbnail_selector.thumbnail_input = "#manual_thumbnail"]
 * @param [selectors.auto_thumbnail_selector.section = "#auto_thumbnail_section"]
 * @param [selectors.auto_thumbnail_selector.thumbnail_input = "#auto_thumbnail"]
 * @param [selectors.auto_thumbnail_selector.thumbnail_preview = "#thumbnail_preview"]
 * @param [selectors.auto_thumbnail_selector.thumbnail_link = "#youtube_thumbnail_link"]
 * @param [selectors.auto_thumbnail_selector.video_time_slider_section = "#video_time_slider"]
 * @param [selectors.auto_thumbnail_selector.current_thumbnail = "#current_thumbnail"]
 * @param [selectors.auto_thumbnail_selector.new_video_upload = "#new_video_upload"]
 * @param [selectors.duration_selector = "#duration"]
 * @param [selectors.youtube_label = "#youtube_label"]
 * @param confirmation_swal_options
 * @param fetch_youtube_api_url
 * @param fetch_youtube_api_required_msg
 * @param thumbnail_switch_label_after_fetched
 */
function initFetchYoutubeAPI(
    selectors = {},
    confirmation_swal_options,
    fetch_youtube_api_url,
    fetch_youtube_api_required_msg,
    thumbnail_switch_label_after_fetched
) {
    const default_selectors = {
        button: "#fetch_youtube_data_btn",
        video_section_selector: "#video_section",
        video_url: "#youtube_url",
        video_id: "#youtube_video_id",
        video_title: "#video_title",
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
            thumbnail_link: "#youtube_thumbnail_link",
            video_time_slider_section: "#video_time_slider",

            // Edit page only
            current_thumbnail: "#current_thumbnail",
            new_video_upload: "#new_video_upload",
        },
        duration_selector: "#duration",
        youtube_label: "#youtube_label",
    }

    selectors = mergeObject(default_selectors, selectors);

    async function swalConfirmation() {
        if(!('showCancelButton' in confirmation_swal_options)) {
            confirmation_swal_options.showCancelButton = true;
        }
        if(!('reverseButtons' in confirmation_swal_options)) {
            confirmation_swal_options.reverseButtons = true;
        }
        if(!('buttonsStyling' in confirmation_swal_options)) {
            confirmation_swal_options.buttonsStyling = false;
        }
        if(!('customClass' in confirmation_swal_options)) {
            confirmation_swal_options.customClass = {
                popup: "swal2-warning",
                confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                cancelButton: "btn btn-custom-light btn-active-custom-light min-w-60px min-w-lg-150px min-h-40px rounded-1 btn-outline"
            }
        } else {
            if(!('popup' in confirmation_swal_options.customClass)) {
                confirmation_swal_options.customClass.popup = "swal2-warning";
            }
            if(!('confirmButton' in confirmation_swal_options.customClass)) {
                confirmation_swal_options.customClass.confirmButton = "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1";
            }
            if(!('cancelButton' in confirmation_swal_options.customClass)) {
                confirmation_swal_options.customClass.cancelButton = "btn btn-custom-light btn-active-custom-light min-w-60px min-w-lg-150px min-h-40px rounded-1 btn-outline";
            }
        }

        return await swal.fire({
            ...confirmation_swal_options
        }).then((result) => {
            return !!result.value;
        })
    }

    async function handleFetchYoutubeAPI(event) {
        event.preventDefault();
        const form_type = ($($(selectors.video_url).prop("form")).attr("id").split("_")[0] ?? "");
        const youtube_url = $(selectors.video_url).val();
        const youtube_video_id = $(selectors.video_id).val();
        const new_video_upload = $(selectors.auto_thumbnail_selector.new_video_upload).val();

        if (youtube_url !== "") {
            // Get form is new or edit
            const confirmation = (form_type === "create" && youtube_video_id !== "") || (form_type === "edit" && new_video_upload === "1") ? await swalConfirmation() : true;

            if(confirmation) {
                // Show spinner
                SpinnerSingletonFactory.block();

                await axios.get(fetch_youtube_api_url, { params: { youtube_url: youtube_url } })
                    .then(function (response) {
                        $(selectors.video_id).val(response.data.id);
                        $(selectors.video_title).val(response.data.title);
                        $(selectors.thumbnail_section_selector).show();

                        if(response.data.thumbnail.link !== "") {
                            if (form_type === "edit") {
                                $(selectors.auto_thumbnail_selector.new_video_upload).val("1");
                                $(selectors.auto_thumbnail_selector.current_thumbnail).hide();
                            }
                            $(selectors.auto_thumbnail_selector.thumbnail_preview).attr("src", response.data.thumbnail.link);
                            $(selectors.auto_thumbnail_selector.thumbnail_link).val(response.data.thumbnail.link);
                            $(selectors.auto_thumbnail_selector.thumbnail_input).val(response.data.thumbnail.base64);
                            $(selectors.auto_thumbnail_selector.video_time_slider_section).hide();
                            $(selectors.thumbnail_switch_selector).prop("checked", true).trigger("change");
                        } else {
                            if (form_type === "edit") {
                                $(selectors.auto_thumbnail_selector.new_video_upload).val("0");
                            }
                            $(selectors.auto_thumbnail_selector.thumbnail_preview).attr("src", "");
                            $(selectors.auto_thumbnail_selector.thumbnail_link).val("");
                            $(selectors.auto_thumbnail_selector.thumbnail_input).val("");
                            $(selectors.auto_thumbnail_selector.video_time_slider_section).hide();
                            $(selectors.thumbnail_switch_selector).prop("disabled", true).prop("checked", false).trigger("change");
                        }

                        $(selectors.duration_selector).val(response.data.duration);
                        $(selectors.video_section_selector).addClass("col-6").removeClass("col-12");
                        $(selectors.youtube_label).addClass("h-60px h-sm-45px");

                        // Change thumbnail switch label
                        $(selectors.thumbnail_switch_label_selector).html(thumbnail_switch_label_after_fetched);
                    })
                    .catch(function (error) {
                        toastr.error(error.response.data.message);
                    })
                    .finally(function () {
                        // Hide spinner
                        SpinnerSingletonFactory.unblock();
                    });
            }
        } else {
            toastr.error(fetch_youtube_api_required_msg);
        }
    }

    $(selectors.button).on("click", handleFetchYoutubeAPI);
}
// jshint ignore:end
