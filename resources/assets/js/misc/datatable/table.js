import changeActiveStateWithSwitch from './switch'
import deleteItemFromDataTable from './delete'
import initSwitchery from './switchery'

$.fn.tooltip && $('[data-toggle="tooltip"]').tooltip();
$.fn.editable.defaults.mode = 'inline';
$.fn.editableform.buttons =
    '<button type="submit" class="btn btn-success editable-submit btn-sm"><i class="fa fa-check"></i></button>' +
    '<button type="button" class="btn editable-cancel btn-mini btn-sm"><i class="fa fa-close"></i></button>';

for (let index in datatables) {
    let config = datatables[index];
    /**
     *
     * @returns {jQuery}
     */
    let loadDataTable = function () {
        return $(config.selector).DataTable({
            processing: true,
            serverSide: true,
            ajax: config.has_tabs ? $(config.selector).data('url') : config.slug + '/data-table',
            columns: config.columns,
            order: $.parseJSON(config.order)
        }).on('draw.dt', function () {
            $('.js-editable').editable({
                error: function (response) {
                    if (response.status !== 422) {
                        return 'Something went wrong, please, try again later.';
                    }

                    return response.responseJSON.message;
                }
            });
            $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip();

            initSwitchery();
        });
    };

    let dataTable = $();
    if (!config.has_tabs) {
        dataTable = loadDataTable();
		
		// Alert for delete button
		deleteItemFromDataTable(dataTable);
		// Automatically update switch states in backend
		changeActiveStateWithSwitch(dataTable);
    } else {
        let onTabChange = function () {
            if ($.fn.DataTable.isDataTable(config.selector)) {
                return;
            }

            dataTable = loadDataTable();
			
			// Alert for delete button
			deleteItemFromDataTable(dataTable);
			// Automatically update switch states in backend
			changeActiveStateWithSwitch(dataTable);
        };

        // When page loads, we initialize first tab
        onTabChange();

        // When tabs are clicked, we load info there
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            onTabChange();
        });
    }
}