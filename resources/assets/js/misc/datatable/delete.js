export default (dataTable) => {
    $(document).on('click', '.js-delete-resource', async (e) => {
        e.preventDefault();
        let url = $(e.target).data('url');

        let result = await swal({
            title: 'Are you sure?',
            text: 'Once deleted, you will not be able to recover this resource!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        });

        if (result.value) {
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _method: 'delete',
                }
            }).done(() => {
                dataTable.api().ajax.reload();

				swal('Item has been deleted!');
			});
        }
    });
}