function initDataTableRowRearrange(tableId = 'dataTable', api, submitUrl, params = []) {
    const table = api;

    // create new hidden input hidden
    $('body').append('<input type="hidden" name="'+ tableId +'neworder" id="'+ tableId +'neworder" />');

    $(document).on('click', '.rearrange-items', function() {
        $('#' + tableId +' tbody').addClass('sortable');
        const header = $('#'+ tableId  +'_wrapper').parent().parent().find('.card-header');
        const inputs = header.find('input');
        const selects = header.find('select');

        // Sortable rows
        var sort = $('#' + tableId).sortable({
            containerSelector: 'table',
            itemPath: '> tbody',
            itemSelector: 'tr',
            placeholder: '<tr class="placeholder"/>',
            onDrop: function ($item, container, _super, event) {
                $item.removeClass(container.group.options.draggedClass).removeAttr("style");
                $("body").removeClass(container.group.options.bodyClass);
                var neworder = sort.sortable("serialize").get();
                $('#'+ tableId +'neworder').val(JSON.stringify(neworder, null, 2));
            }
        });

        $('#'+ tableId +'neworder').val(JSON.stringify(sort.sortable("serialize").get()));

        table.search('').columns().search('').draw();
        table.order([0, 'asc']).draw();
        table.page.len(-1).draw(); // show 'All'
        table.column(0).visible(true); // show sorting icon

        inputs.val(''); // clear all input and select
        selects.val('').trigger("change");
        inputs.prop("disabled", true);
        selects.prop("disabled", true);

        $('#' + tableId + '_wrapper .tools').hide();
        $('#actionBtn').hide();
        $('#arrangeBtn').show();

        cloneThead(tableId);
        toggleTableHeaderState(tableId, table, true);
    })

    $(document).on('click', '.cancel-rearrange-items', function() {
        toggleRearrangeButtons(tableId, true, true);
        closeDataTableRowRearrange(tableId, table);
    })

    $(document).on('click', '.save-rearrange-items', function() {
        const new_order = $('#'+ tableId +'neworder').val();
        const button = $(this)[0];

        // Show loading indication
        $(this)[0].setAttribute('data-kt-indicator', 'on');

        toggleRearrangeButtons(tableId, true, true);

        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: {
                new_order: new_order,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                toastr.success(data.success ?? 'Success');
            },
            error: function (data) {
                toastr.error(data.error ?? 'Error');
                toggleRearrangeButtons(tableId, true, false);
            },
            complete: function () {
                // Hide loading indication
                button.removeAttribute('data-kt-indicator');
                closeDataTableRowRearrange(tableId, table);
            }
        });
    })
}

function closeDataTableRowRearrange(tableId, table) {
    toggleTableHeaderState(tableId, table, false);

    $('#' + tableId + ' tbody').removeClass('sortable');

    $('#' + tableId).sortable("destroy");

    table.page.len(50).draw(); // show '25'
    table.column(0).visible(false); // hide sorting icon

    table.ajax.reload();
}

function refreshDataTableRowRearrange(tableId, api) {
    const table = api;
    const header = $('#'+ tableId  +'_wrapper').parent().parent().find('.card-header');
    const toolbar = header.find('.card-toolbar');
    const footer = $('#'+ tableId  +'_wrapper .dataTables_length').parent().parent();

    // cloneThead(tableId);

    // check if tbody has class sortable
    if ($('#' + tableId + ' tbody').hasClass('sortable')) {
        // blur column
        $('#' + tableId + ' tbody tr').each(function() {
            $(this).find('td').not('.no-blur').addClass('arrange-blur');
        });

        header.find('.card-title').children().addClass('d-none');
        toolbar.find('button').addClass('d-none');
        toolbar.find('a').addClass('d-none');

        // toggleTableHeaderState(tableId, table, true)
        toggleRearrangeButtons(tableId);

        footer.hide();
    } else {
        header.find('.card-title').children().removeClass("d-none");
        toolbar.find('button').removeClass("d-none");
        toolbar.find('a').removeClass("d-none");

        // toggleTableHeaderState(tableId, table, false)
        toggleRearrangeButtons(tableId, false);

        footer.show();
    }
}

function toggleRearrangeButtons(tableId, show = true, disabled = false) {
    const header = $('#'+ tableId  +'_wrapper').parent().parent().find('.card-header');
    const cancelBtn = header.find('.cancel-rearrange-items');
    const saveBtn = header.find('.save-rearrange-items');
    const inputs = header.find('input');
    const selects = header.find('select');

    if (show) {
        cancelBtn.removeClass("d-none");
        saveBtn.removeClass("d-none");
    } else {
        cancelBtn.addClass("d-none");
        saveBtn.addClass("d-none");
    }

    cancelBtn.prop("disabled", disabled);
    saveBtn.prop("disabled", disabled);

    if(disabled) {
        cancelBtn.addClass("disabled");
        saveBtn.addClass("disabled");
    } else {
        cancelBtn.removeClass("disabled");
        saveBtn.removeClass("disabled");
    }

    inputs.prop("disabled", disabled);
    selects.prop("disabled", disabled);
}

function cloneThead(tableId) {
    // remove old rearrange-thead
    $('#'+ tableId +' .rearrange-thead').remove();

    // copy thead and hide
    const thead = $('#' + tableId + ' thead');
    const rearrangeThead = thead.clone();
    // add class to thead
    rearrangeThead.addClass('d-none');
    rearrangeThead.addClass('rearrange-thead');
    rearrangeThead.find('th').first().addClass('text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0');
    // remove class
    rearrangeThead.find('th').removeClass('sorting');
    rearrangeThead.find('th').removeClass('sorting_asc');
    rearrangeThead.find('th').removeClass('sorting_desc');
    thead.after(rearrangeThead);
}

function toggleTableHeaderState(tableId, table, disabled = false) {
    const thead = $('#' + tableId + ' thead');
    const rearrangeThead = $('#' + tableId + ' .rearrange-thead');

    if(disabled) {
        thead.addClass('d-none');
        rearrangeThead.removeClass('d-none');
    } else {
        thead.removeClass('d-none');
        rearrangeThead.addClass('d-none');
    }
}
