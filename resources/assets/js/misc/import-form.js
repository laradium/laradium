$(function () {
    let $form = $('.import-form')
    let $button = $form.find('button.import-button')
    let $input = $button.parent().find('input[name=import]')

    if (!$form.length || !$button.length || !$input.length) {
        return
    }

    $button.on('click', () => $input.trigger('click'))

    $input.on('change', e => {
        let files = e.currentTarget.files

        if (files.length) {
            let file = files[0]

            swal({
                type: 'warning',
                title: 'Are you sure?',
                text: `Do you really want to upload ${file.name}`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then(res => {
                if (!res.value || res.dismiss) {
                    $form[0].reset()
                } else {
                    $form.submit()
                }
            })
        }
    })
})