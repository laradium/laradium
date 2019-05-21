<template>
    <table class="resource-datatable table table-bordered">
        <thead>
            <tr>
                <th v-for="column in field.table.base_columns"
                    v-html="column.pretty_name"
                    :style="'width: ' + column.width + ';'"
                ></th>
            </tr>
        </thead>
    </table>
</template>

<script>
    import changeActiveStateWithSwitch from '../../misc/datatable/switch'
    import deleteItemFromDataTable from '../../misc/datatable/delete'
    import initSwitchery from '../../misc/datatable/switchery'

    export default {
        name: 'Table',

        props: ['field', 'language'],

        data() {
            return {
            };
        },

        mounted() {
            $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip();
            $.fn.editable.defaults.mode = 'inline';
            $.fn.editableform.buttons =
                '<button type="submit" class="btn btn-success editable-submit btn-sm"><i class="fa fa-check"></i></button>' +
                '<button type="button" class="btn editable-cancel btn-mini btn-sm"><i class="fa fa-close"></i></button>';


            let datatable = $(this.$el).dataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: this.field.table.url,
                columns: this.field.
                    table.columns,
                order: this.field.order
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

            deleteItemFromDataTable(datatable);
            changeActiveStateWithSwitch(datatable, this.field.table.toggle_url);
        },

        methods: {

        }
    }
</script>
