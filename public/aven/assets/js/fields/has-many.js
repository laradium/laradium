$(function () {
    $('.js-add-hasMany').on('click', function (e) {
        e.preventDefault();
        let wrapper = $('.js-has-many-wrapper').clone();
        var id = guid();
        $('.form-group', wrapper).addClass('js-block-' + id);

        $.each(fieldList, function (index, item) {
            let field = '';
            if (item.isTranslatable) {
                field = $('#field-template-' + item.type + ' .js-text-translatable').clone();
            } else {
                field = $('#field-template-' + item.type + ' .js-text-non-translatable').clone();
            }
            if(item.type == 'select') {
                let select = field.find('select');
                $.each(item.options, function (value, name) {
                    select.append($('<option>', {
                        value: value,
                        text : name,
                    }));
                });
            }
            if (item.isTranslatable) {
                let fields = field.find('.js-tab .js-field');
                let attribute = '';
                $.each(fields, function (index, f) {
                    attribute = item.nameAttribute;
                    $(f).attr('name', attribute.replace('__LOCALE__', $(f).data('locale')).replace('__ID__', id))
                });
            } else {
                field.find('input,select,textarea').attr('name', item.nameAttribute.replace('__ID__', id));
            }
            field = field.html();
            field = field.replace('__FIELD_NAME__', item.name);
            wrapper.find('.js-fields').append(field);
        });

        $('.js-has-many-list').append(wrapper.html().replace('__ITEM_ID__', id));

        var elems = document.querySelectorAll('.js-switch');

        elems.forEach(function(html) {
            new Switchery(html);
        });
    });

    $(document).on('click', '.js-remove-has-many-item', function (e) {
        e.preventDefault();
        let id = $(this).data('id');

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this item!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    if (!isNaN(parseInt(id))) {
                        $.ajax({
                            type: 'POST',
                            url: $(this).data('url'),
                            data: {
                                _method: 'delete',
                                id: id
                            }
                        });
                    }

                    $('.js-block-' + id).slideUp().remove();


                    swal("Item has been deleted!", {
                        icon: "success",
                    });
                }
            });

    });

    function guid() {
        return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
    }
});