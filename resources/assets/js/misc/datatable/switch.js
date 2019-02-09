exports.changeActiveStateWithSwitch = () => {
    $(document).on('change', '.js-switch', function () {
        let id = $(this).data('id');
        let column = $(this).attr('name');

        $.post(datatable_config.slug + '/toggle/' + id, {
            column: column
        }, function () {
            try {
                toastr.success('Resource successfully updated');
            } catch (e) {
                console.log('Failed to init toastr');
            }
        })
    });
}