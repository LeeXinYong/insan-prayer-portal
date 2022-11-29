// jshint ignore:start
window.SpinnerSingletonFactory = (function(){
    const defaultMessage = '<div class="blockui-message"><span class="spinner-border text-primary"></span> Processing...</div>';
    let instance;
    return {
        getInstance: function(target = "#kt_body"){
            if (instance == null) {
                instance = new KTBlockUI($(target)[0], {
                    overlayClass: 'position-fixed',
                    overlayColor: '#000000',
                    message: defaultMessage
                });
                // KTBlockUI.constructor = null;
            }
            return instance;
        },
        isBlocked: function() {
            return this.getInstance().blocked;
        },
        block: function (message = null) {
            if (message === null) {
                this.getInstance().options.message = defaultMessage;
            } else {
                this.getInstance().options.message = '<div class="blockui-message"><span class="spinner-border text-primary"></span>' + message + '</div>'
            }

            if(!this.isBlocked()) {
                this.getInstance().block();
            }
        },
        unblock: function () {
            if(this.isBlocked()) {
                this.getInstance().release();
            }
        },
        release: function () {
            this.unblock();
        },
    };
})();
// jshint ignore:end
