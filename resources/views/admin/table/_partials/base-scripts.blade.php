<!-- Required datatable js -->
<script src="/laradium/admin/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/laradium/admin/assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<!-- Responsive examples -->
<script src="/laradium/admin/assets/plugins/datatables/dataTables.responsive.min.js"></script>
<script src="/laradium/admin/assets/plugins/datatables/responsive.bootstrap4.min.js"></script>
<script>
    function initSwitchery() {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

        elems.forEach(function (html) {
            if (!$(html).data('switchery')) {
                new Switchery(html, {
                    disabled: $(html).data('disabled') === 'yes' ? true : false
                });
            }
        });
    }

    $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip()
    $.fn.editable.defaults.mode = 'inline';
    $.fn.editableform.buttons =
        '<button type="submit" class="btn btn-success editable-submit btn-sm"><i class="fa fa-check"></i></button>' +
        '<button type="button" class="btn editable-cancel btn-mini btn-sm"><i class="fa fa-close"></i></button>';

    var datatables = [];
    var datatable_config = {};
</script>