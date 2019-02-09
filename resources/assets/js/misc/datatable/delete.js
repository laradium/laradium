exports.deleteItemFromDataTable = (dataTable) => {
    $(document).on('click', '.js-delete-resource', function (e) {
        e.preventDefault();
        let url = $(this).data('url');

        swal({
            title: 'Are you sure?',
            text: 'Once deleted, you will not be able to recover this resource!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        })
            .then((result) => {
                if (result.value) {

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            _method: 'delete',
                        }
                    });

                    dataTable.ajax.reload(); // TODO: Fix this for multiple dts

                    swal('Item has been deleted!', {
                        icon: "success",
                    });
                }
            });
    });
}