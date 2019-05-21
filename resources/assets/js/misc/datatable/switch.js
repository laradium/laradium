export default (datatable, toggleUrl) => {
    $(document).on('change', '.js-switch', (event) => {
        let id = $(event.target).data('id');
        let column = $(event.target).attr('name');

        $.post(toggleUrl + '/' + id, {
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