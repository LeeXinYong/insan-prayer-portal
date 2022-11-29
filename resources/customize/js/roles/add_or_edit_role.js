"use strict";

// Class definition
const AddOrEditRoleName = function () {
    var submitButton;
    var cancelButton;
    var validator;
    var form;
    var modal;
    var modalEl;
    var modalOptions = {
        modalSelector: '#roleModal',
        modalTitleSelector: '#roleModalTitle',
        modalTitle: null,

        submitButtonSelector: '#roleModalFormSubmitButton',
        cancelButtonSelector: '#roleModalFormCancelButton',
        submitButtonText: "Add",
        cancelButtonText: "Cancel",

        modalFormSelector: '#roleModalForm',
        persistValues: true,
        defaultValues: {
            name: '',
        },
        validatorMessage: {
            name: {
                required: 'Please enter a role name.'
            }
        },
        formSubmission: {
            url: null,
            method: 'POST',
        },

        initCallback: null,

        successMessage: null,
        errorMessage: null,
        successCallback: null,
        resultButtonText: "Ok"
    };

    // Handle form validation and submittion
    var handleForm = function () {
        // Stepper custom navigation

        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'name': {
                        validators: {
                            notEmpty: {
                                message: modalOptions.validatorMessage.name.required
                            }
                        }
                    },
                },

                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );

        // Action buttons
        form.addEventListener('submit', function (e) {
            // Prevent default button action
            e.preventDefault();

            // Validate form before submit
            if (validator) {
                validator.validate()
                    .then(function (status) {
                        if (status == 'Valid') {
                            // Show loading indication
                            submitButton.setAttribute('data-kt-indicator', 'on');

                            // Disable button to avoid multiple click
                            submitButton.disabled = true;

                            if (!modalOptions.formSubmission.url) {
                                throw new Error('Form submission url is not defined.');
                            }

                            // Submit form
                            axios({
                                method: modalOptions.formSubmission.method,
                                url: modalOptions.formSubmission.url,
                                data: Object.fromEntries(new FormData(form)),
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }).then(function (response) {
                                Swal.fire({
                                    title: modalOptions.successMessage || response.data.message || 'Success',
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: modalOptions.resultButtonText || 'Ok',
                                    customClass: {
                                        popup: "swal2-success",
                                        confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1"
                                    }
                                });

                                if (modalOptions.persistValues) {
                                    for (var [key, value] of Object.entries(response.data.data)) {
                                        form.querySelector(`[name="${key}"]`).defaultValue = value;
                                    }
                                } else {
                                    form.reset();
                                }

                                modal.hide();

                                if (modalOptions.successCallback) {
                                    modalOptions.successCallback(response);
                                }
                            }).catch(function (error) {
                                var errorMessage = error.response?.data?.message || (error.response?.data?.errors !== undefined ? Object.values(error.response?.data?.errors)?.[0]?.[0] : error.response?.data?.error);

                                Swal.fire({
                                    title: modalOptions.errorMessage || errorMessage || 'Please check the form',
                                    buttonsStyling: false,
                                    confirmButtonText: modalOptions.resultButtonText || 'Ok',
                                    customClass: {
                                        popup: "swal2-danger",
                                        confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1"
                                    }
                                });
                            }).finally(function () {
                                // Remove loading indication
                                submitButton.removeAttribute('data-kt-indicator');

                                // Enable button
                                submitButton.disabled = false;

                            });

                        } else {
                            // Show popup warning. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                            Swal.fire({
                                title: modalOptions.errorMessage || 'Please check the form',
                                buttonsStyling: false,
                                confirmButtonText: modalOptions.resultButtonText || 'Ok',
                                customClass: {
                                    popup: "swal2-danger",
                                    confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1"
                                }
                            });
                        }
                    });
            }
        });

        modalEl.addEventListener('hidden.bs.modal', function (e) {
            form.reset(); // Reset form
        });
    };

    var init = function (options) {
        modalOptions = mergeOptions(modalOptions, options);

        // Elements
        modalEl = document.querySelector(modalOptions.modalSelector);

        if (!modalEl) {
            return;
        }

        modal = new bootstrap.Modal(modalEl);

        form = document.querySelector(modalOptions.modalFormSelector);
        submitButton = document.querySelector(modalOptions.submitButtonSelector);
        cancelButton = document.querySelector(modalOptions.cancelButtonSelector);

        document.querySelector(modalOptions.modalTitleSelector).innerText = modalOptions.modalTitle;
        submitButton.querySelector(".indicator-label").innerText = modalOptions.submitButtonText;
        cancelButton.innerText = modalOptions.cancelButtonText;
        for (var [key, value] of Object.entries(modalOptions.defaultValues)) {
            form.querySelector(`[name="${key}"]`).defaultValue = value;
        }

        handleForm();

        if (modalOptions.initCallback) {
            modalOptions.initCallback(modal);
        }
    };

    function isObject(item) {
        return (item && typeof item === 'object' && !Array.isArray(item));
    }

    function mergeOptions(target, ...sources) {
        if (!sources.length) return target;
        const source = sources.shift();

        if (isObject(target) && isObject(source)) {
            for (const key in source) {
                if (isObject(source[key])) {
                    if (!target[key]) Object.assign(target, {[key]: {}});
                    mergeOptions(target[key], source[key]);
                } else {
                    Object.assign(target, {[key]: source[key]});
                }
            }
        }

        return mergeOptions(target, ...sources);
    }

    return {
        // Public functions
        EditRole: function (options = {}) {
            init(mergeOptions({
                modalTitle: "Edit Role",
                submitButtonText: "Update",
            }, options));
        },
        AddRole: function (options = {}) {
            init(mergeOptions({
                modalTitle: "Add Role",
                submitButtonText: "Add",
            }, options));
        },
    };
}();
