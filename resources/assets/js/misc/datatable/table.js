import changeActiveStateWithSwitch from './switch'
import deleteItemFromDataTable from './delete'

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
            order: config.order
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
    } else {
        let onTabChange = function () {
            if ($.fn.DataTable.isDataTable(config.selector)) {
                return;
            }

            dataTable = loadDataTable();
        };

        // When page loads, we initialize first tab
        onTabChange();

        // When tabs are clicked, we load info there
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            onTabChange();
        });
    }

    // Alert for delete button
    deleteItemFromDataTable(dataTable);
    // Automatically update switch states in backend
    changeActiveStateWithSwitch(dataTable);
}