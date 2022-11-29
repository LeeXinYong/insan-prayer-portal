// jshint ignore:start
function initSwitchLabel(selector, checked_label, unchecked_label, textSelector = null) {
    if (null === textSelector) {
        textSelector = selector + "_text";
    }
    $(selector).on("click manual_change", function() {
        if($(this).prop("checked")) {
            $(textSelector).html(checked_label);
        } else {
            $(textSelector).html(unchecked_label);
        }
    });
}

function initWYSIWYG(selector, options, template = true) {
    tinymce.init({
        selector: selector,
        setup: function (editor) {
            editor.on('change', function () {
                tinymce.triggerSave();
            });
        },
        branding: false,
        menubar: false,
        toolbar: [
            "styleselect fontselect fontsizeselect forecolor backcolor | bold italic underline | link image emoticons",
            "alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | blockquote subscript superscript | table advlist | autolink | lists | preview code"
        ],
        plugins: "table advlist autolink link image emoticons lists charmap print preview code paste",
        font_formats:
            "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Roboto=roboto; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats",
        content_style:
            "@import url('https://fonts.googleapis.com/css?family=Roboto:300,400,600,700'); body { font-family: Roboto; font-size: 14pt }",
        file_picker_types: 'image',
        /* and here's our custom image picker*/
        file_picker_callback: function (cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            /*
            Note: In modern browsers input[type="file"] is functional without
            even adding it to the DOM, but that might not be the case in some older
            or quirky browsers like IE, so you might want to add it to the DOM
            just in case, and visually hide it. And do not forget do remove it
            once you do not need it anymore.
            */

            input.onchange = function () {
                var file = this.files[0];

                var reader = new FileReader();
                reader.onload = function () {
                    /*
                    Note: Now we need to register the blob in TinyMCEs image blob
                    registry. In the next release this part hopefully won't be
                    necessary, as we are looking to handle it internally.
                    */
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);

                    /* call the callback and populate the Title field with the file name */
                    cb(blobInfo.blobUri(), {
                        title: file.name
                    });
                };
                reader.readAsDataURL(file);
            };

            input.click();
        },
        init_instance_callback: (editor) => {
            if(editor.getContent() === '') {
                axios.get('/wysiwyg_html_template.html')
                    .then(function (response) {
                        editor.setContent(response.data);
                        tinymce.triggerSave();
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        },
        ...options,
    });
}

/**
 *
 * @param form
 * @param spinner_message
 * @param error_swal_title
 * @param options
 * @param options.submitButton
 * @param options.url
 * @param {bool} [options.useFormOnSubmit = true]
 * @param {bool} [options.useButtonOnClick = false]
 * @param {bool} [options.skipValidation = false]
 * @param {function} options.callbacks.beforeSubmit
 * @param {function} options.callbacks.afterSubmit
 * @param {function} options.callbacks.onSubmitErrors
 * @param {bool} [options.appendBeforeSubmitResult = false]
 */
function initFormSubmission(form, spinner_message, error_swal_title, options = {}) {
    // Private variables
    let submitButton = $(options?.submitButton || form.find("button[type='submit']"));
    let useFormOnSubmit = options?.useFormOnSubmit ?? true;
    let useButtonOnClick = options?.useButtonOnClick ?? false;
    let skipValidation = options?.skipValidation ?? false;
    let appendBeforeSubmitResult = options?.appendBeforeSubmitResult ?? false;
    let url = options?.url || form.attr("action");
    let mergeData = options?.merge_data || {};
    let onSubmitErrors = options?.callbacks?.onSubmitErrors || function(swaltitle, errors) {
        Swal.fire({
            title: swaltitle,
            buttonsStyling: false,
            allowOutsideClick: false,
            showCancelButton: false,
            customClass: {
                popup: "swal2-success",
                confirmButton:"btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
            },
            html:
                `<ul class="text-danger text-start">` + errors + `</ul>`,
        });
    };
    let useToastOnSuccess = options?.useToastOnSuccess ?? false;

    async function beforeSubmit() {
        if (typeof options?.callbacks?.beforeSubmit === "function") {
            const result = await options.callbacks.beforeSubmit();
            if (result !== false) {
                return result;
            }
            return false;
        }
        return true;
    }

    async function submit(beforeSubmitResult) {
        // Show loading indication
        submitButton.attr("data-kt-indicator", "on");

        // Disable submit button
        submitButton.prop("disabled", true);

        // Show spinner
        SpinnerSingletonFactory.block(spinner_message);

        var resolved;

        let formData = new FormData(form[0]);
        Object.entries(mergeData).forEach(([key, value]) => {
            formData.append(key, value);
        });

        if (appendBeforeSubmitResult) {
            Object.entries(beforeSubmitResult).forEach(([key, value]) => {
                formData.append(key, value);
            });
        }

        // Send ajax request
        await axios.post(url, formData)
            .then(function (response) {
                if (useToastOnSuccess) {
                    toastr.success(response.data.success);
                } else {
                    // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    Swal.fire({
                        title: response.data.success,
                        buttonsStyling: false,
                        allowOutsideClick: false,
                        showCancelButton: false,
                        confirmButtonText: response.data.button,
                        customClass: {
                            popup: "swal2-success",
                            confirmButton:"btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                        }
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            if(response.data.redirect != null && response.data.redirect != "") {
                                if(window.location.href === response.data.redirect) {
                                    window.location.reload();
                                } else {
                                    window.location.replace(response.data.redirect);
                                }
                            } else {
                                Swal.close();
                            }
                        }
                    });
                }

                resolved = true;
            })
            .catch(function (error) {
                let res;
                let errors = "";
                let swaltitle = error_swal_title;
                $.each(error.response.data.errors || error.response.data.error, function(attribute, error) {
                    $.each(error, function(index, value) {
                        // get SWAL title if any
                        const re = /SWAL_TITLE:s*([^;]*)/gi;
                        if ((res = re.exec(value)) !== null) {
                            swaltitle = (res.length > 1) ? res[1] : swaltitle;
                        } else {
                            errors += "<li><b>" + value + "</b></li>";
                        }
                    });
                });

                Swal.close();
                onSubmitErrors(swaltitle, errors, handleSubmission, error);

                resolved = false;
            })
            .finally(function () {
                // always executed
                // Hide loading indication
                submitButton.removeAttr("data-kt-indicator");

                // Enable submit button
                submitButton.prop("disabled", false);

                // Hide spinner
                SpinnerSingletonFactory.unblock();
            });

        return resolved;
    }

    async function afterSubmit (submitResult) {
        if (typeof options?.callbacks?.afterSubmit === "function") {
            const result = await options.callbacks.afterSubmit(submitResult);
            if (result !== false) {
                return result;
            }
            return false;
        }
        return false;
    }

    async function handleSubmission(event = null) {
        if (event !== null) {
            event.preventDefault();
        }

        if (form[0].reportValidity() || skipValidation) {

            if (options.hasOwnProperty("url")) {
                if (typeof options.url === "function") {
                    url = options.url();
                } else {
                    url = options.url;
                }
            }

            const beforeSubmitResult = await beforeSubmit();

            if (beforeSubmitResult === false) {
                return;
            }

            const submitResult = await submit(beforeSubmitResult);

            afterSubmit(submitResult);
        }

        // const invalid_element = form.find(":invalid");

        // if (!invalid_element.length) {
        //     const beforeSubmitResult = await beforeSubmit();

        //     if (beforeSubmitResult === false) {
        //         return;
        //     }

        //     const submitResult = await submit();

        //     afterSubmit(submitResult);
        // } else {
        //     // If not, show error message
        //     invalid_element.first()[0].reportValidity();
        // }
    }

    if (useFormOnSubmit)
        form.on("submit", handleSubmission)

    if (useButtonOnClick)
        submitButton.on("click", handleSubmission)

}

function initDelete(
    selector,
    confirmation_swal_options,
    spinner_message,
    delete_url,
    csrf_token
) {
    $(selector).on("click", function() {
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
                popup: "swal2-danger",
                confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                cancelButton: "btn btn-custom-light btn-active-custom-light min-w-60px min-w-lg-150px min-h-40px rounded-1 btn-outline"
            }
        } else {
            if(!('popup' in confirmation_swal_options.customClass)) {
                confirmation_swal_options.customClass.popup = "swal2-danger";
            }
            if(!('confirmButton' in confirmation_swal_options.customClass)) {
                confirmation_swal_options.customClass.confirmButton = "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1";
            }
            if(!('cancelButton' in confirmation_swal_options.customClass)) {
                confirmation_swal_options.customClass.cancelButton = "btn btn-custom-light btn-active-custom-light min-w-60px min-w-lg-150px min-h-40px rounded-1 btn-outline";
            }
        }

        swal.fire({
            ...confirmation_swal_options
        }).then((result) => {
            if (result.value) {
                // Show loading indication
                $(this).attr("data-kt-indicator", "on");

                // Disable button
                $(this).prop("disabled", true);

                // Show spinner
                SpinnerSingletonFactory.block(spinner_message);

                // Create CSRF token hidden input
                let csrf_input = document.createElement("input");
                csrf_input.setAttribute("type", "hidden");
                csrf_input.setAttribute("name", "_token");
                csrf_input.setAttribute("value", csrf_token);

                // Create DELETE method hidden input
                let method_input = document.createElement("input");
                method_input.setAttribute("type", "hidden");
                method_input.setAttribute("name", "_method");
                method_input.setAttribute("value", "DELETE");

                // Create delete form
                let form = document.createElement("form");
                form.setAttribute("action", delete_url);
                form.setAttribute("method", "POST");
                form.appendChild(csrf_input);
                form.appendChild(method_input);

                // Append delete form to body and submit
                $("body").append(form);
                $(form).submit();
            }
        })
    })
}

function getCountryTimezone(action_path, spinner_message = null, selected_timezone = null) {
    // Show spinner
    SpinnerSingletonFactory.block(spinner_message);

    // Get timezone
    axios.get(action_path)
        .then(function (response) {
            $("#timezone").find("option").not(":first").remove();
            $.each(response.data.timezones, function (index, timezone) {
                if(selected_timezone === timezone.name) {
                    $("#timezone").append(new Option(timezone.timezone_name + " " + timezone.offset, timezone.name, true, true));
                } else {
                    $("#timezone").append(new Option(timezone.timezone_name + " " + timezone.offset, timezone.name));
                }
            });
        })
        .catch(function (error) {
            toastr.error(error.response.data.errors.join("<br>"));
        })
        .then(function () {
            // always executed
            // Hide spinner
            SpinnerSingletonFactory.unblock();
        });
}

function mergeObject(current_object, new_object) {
    let object = {};
    Object.keys(current_object).map(function (key) {
        if(current_object[key] instanceof Object) {
            new_object[key] = mergeObject(current_object[key], new_object[key] ?? {});
        }
        return object[key] = new_object[key] ?? current_object[key];
    });
    return object;
}
// jshint ignore:end
