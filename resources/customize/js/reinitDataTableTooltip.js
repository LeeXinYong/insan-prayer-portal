var dataTableTooltips = [];

function reinitDataTableTooltips(tableId = 'dataTable') {
    (dataTableTooltips[tableId] || []).forEach(function (tooltip) {
        tooltip.dispose();
    });
    dataTableTooltips[tableId] = [];

    var tooltipTriggerList = [].slice.call(document.getElementById(tableId).querySelectorAll('[data-bs-toggle="tooltip"]'));

    dataTableTooltips[tableId] = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return KTApp.initBootstrapTooltip(tooltipTriggerEl, {});
    });
}
