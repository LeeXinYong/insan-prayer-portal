// jshint ignore:start
$(document).ready(() => {
    $("<style type='text/css'>" +
        ".password{padding-right:3rem!important}" +
        ".password.is-invalid,.password.is-valid{padding-right:5.5rem!important}" +
        ".password.is-invalid + .password-reveal,.password.is-valid + .password-reveal{right:2.5rem!important}" +
        "</style>").appendTo("head");

    const passwordSelector = $('input[type="password"]');

    if (!passwordSelector.parent().hasClass("input-group")) {
        passwordSelector.wrap('<div class="input-group"></div>');
    }

    const passwordRevealButton = $('<button class="btn btn-link text-gray-500 password-reveal position-absolute top-50 end-0 mx-2 translate-middle-y bg-transparent z-index-3" type="button"><i class="ra-eye-slash fs-2"></i></button>');

    passwordSelector.addClass('password').after(passwordRevealButton);

    $(".password-reveal").click(function () {
        $(this).blur();
        const password = $(this).siblings('.password');
        if (password.attr('type') === "password") {
            password.attr('type', "text");
            $('i', this).toggleClass("ra-eye ra-eye-slash");
        } else {
            password.attr('type', "password");
            $('i', this).toggleClass("ra-eye ra-eye-slash");
        }
    });
});

// jshint ignore:end
