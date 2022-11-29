// jshint ignore:start
function fireSwal(state = 'success', title, message, html = false, confirmButtonText = null, cancelButtonText = null, onConfirm = null, onCancel = null, options = null) {
    var defaultOptions = {
        title: title,
        customClass:{
            popup: "swal2-"+state,
            confirmButton:"btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
            cancelButton:"btn btn-custom-light btn-active-custom-light min-w-sm-150px min-h-40px rounded-1 btn-outline px-4",
        },
        buttonsStyling: false
    };

    if (message != null) {
        if (html) {
            defaultOptions.html = message;
        } else {
            defaultOptions.text = message;
        }
    }

    if(confirmButtonText != null) {
        defaultOptions.confirmButtonText = confirmButtonText;
    }

    if(cancelButtonText != null) {
        defaultOptions.showCancelButton = true;
        defaultOptions.reverseButtons = true;
        defaultOptions.cancelButtonText = cancelButtonText;
    }

    if (options != null) {
        defaultOptions = Object.assign(defaultOptions, options);
    }

    Swal.fire(defaultOptions).then((result) => {
        if (result.value) {
            if (onConfirm != null) {
                onConfirm();
            }
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            if (onCancel != null) {
                onCancel();
            }
        }
    });
}
// jshint ignore:end
