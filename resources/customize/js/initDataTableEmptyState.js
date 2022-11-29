// jshint ignore:start
function initDataTableEmptyState(tableId = 'dataTable', api, params = [], excludeParamValues = [], hideElementClasses = []) {
    let all_params_empty = true;
    $.each(params, function(index, param) {
        if(api.ajax.params()[param] !== '') {
            if(excludeParamValues.indexOf(api.ajax.params()[param]) === -1) {
                all_params_empty = false;
            }
        }
    });
    if(api.page.info().recordsTotal === 0 && all_params_empty) {
        if(hideElementClasses.length > 0) {
            $.each(hideElementClasses, function(index, element) {
                $(element).addClass('d-none');
            });
        } else {
            $('#'+ tableId  +'_wrapper').parent().parent().find('.card-header').addClass('d-none');
        }

        $('#'+ tableId  +'_wrapper').addClass('d-none');
        $('#'+ tableId  +'EmptyState').removeClass('d-none').addClass('d-flex');
    } else{
        if(hideElementClasses.length > 0) {
            $.each(hideElementClasses, function(index, element) {
                $(element).removeClass('d-none');
            });
        } else {
            $('#'+ tableId  +'_wrapper').parent().parent().find('.card-header').removeClass('d-none');
        }

        $('#'+ tableId  +'_wrapper').removeClass('d-none');
        $('#'+ tableId  +'EmptyState').removeClass('d-flex').addClass('d-none')
    }
}
// jshint ignore:end
