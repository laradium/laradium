@extends('laradium::layouts.main', ['title' => $name, 'table' => $table])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                @include('laradium::admin._partials.messages')

                @if($resource->importInProgress())
                    <div class="alert alert-info">
                        {{ $resource->importStatus() }}
                    </div>
                @endif

                @include('laradium::admin.resource._partials.import_export')

                @if($table->getAdditionalView())
                    <div class="row">
                        <div class="col-md-12">
                            {!! view($table->getAdditionalView(), $table->getAdditionalViewData() )->render() !!}
                        </div>
                    </div>
                    <hr>
                @endif

                @if ($table->getTabs())
                    @foreach($table->getTabs() as $key => $tabs)
                        <ul class="nav nav-tabs">
                            @foreach($tabs as $id => $name)
                                <li class="nav-item">
                                    <a href="#tab-{{ $id }}" data-toggle="tab" aria-expanded="false"
                                       class="nav-link {{ $loop->first ? 'active' : '' }}">
                                        {{ $name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach

                    @foreach($table->getTabs() as $key => $tabs)
                        <div class="tab-content">
                            @foreach ($tabs as $id => $name)
                                <div role="tabpanel" class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                     id="tab-{{ $id }}">

                                    @include('laradium::admin.resource._partials.table', ['dataUrl' => url('/admin/' . $resource->getSlug() . '/data-table?' . $key . '=' . $id) ])

                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    @include('laradium::admin.resource._partials.table')
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Required datatable js -->
    <script src="/laradium/admin/assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/laradium/admin/assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Responsive examples -->
    <script src="/laradium/admin/assets/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="/laradium/admin/assets/plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.0/js/dataTables.rowReorder.min.js"></script>
    <script>
        $(function () {
            $.fn.editable.defaults.mode = 'inline';
            $.fn.editableform.buttons =
                '<button type="submit" class="btn btn-success editable-submit btn-sm"><i class="fa fa-check"></i></button>' +
                '<button type="button" class="btn editable-cancel btn-mini btn-sm"><i class="fa fa-close"></i></button>';

                    @if ($table->getTabs())
            let onTabChange = function (activeTab) {
                    // Entries datatable
                    let selector = '.tab-pane.active .resource-datatable';
                    if ($.fn.DataTable.isDataTable(selector)) {
                        return;
                    }

                    let dataTable = $(selector).DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $(selector).data('url'),
                        columns: {!! $table->getColumnConfig()->toJson() !!},
                        order: [{!! '['.$table->getOrderBy()['key'].', "'.$table->getOrderBy()['direction'].'"]' !!}]
                    }).on('draw.dt', function () {
                        $('.js-editable').editable({});
                        $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip()
                    });
                };

            // When page loads, we initialize first tab
            let activeTab = $('.nav-tabs li.active:first');
            onTabChange(activeTab);

            // When tabs are clicked, we load info there
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                let activeTab = $('.nav-tabs li.active:first');
                onTabChange(activeTab);
            });
                    @else
            let dataTable = $('.resource-datatable').DataTable({!! $table->getTableConfig($resource) !!}).on('draw.dt', function () {
                    $('.js-editable').editable({});
                    $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip()
                });

            @if ($table->isSortable())
            dataTable.on('row-reorder', function (e, diff, edit) {
                var orderData = [];

                for (var i = 0, ien = diff.length; i < ien; i++) {
                    var rowData = dataTable.row(diff[i].node).data();
                    orderData.push({
                        id: rowData.id,
                        position: (diff[i].newPosition + 1) * (dataTable.page.info().page + 1)
                    });
                }

                $.ajax({
                    url: '/admin/{{ $resource->getSlug() }}/data-table/reorder',
                    type: 'POST',
                    data: JSON.stringify(orderData),
                    dataType: 'json',
                    success: function (json) {
                        dataTable.ajax.reload();
                    }
                });
            });
            @endif
            @endif

            $(document).on('click', '.js-delete-resource', function (e) {
                e.preventDefault();
                let url = $(this).data('url');
                swal({
                    title: 'Are you sure?',
                    text: 'Once deleted, you will not be able to recover this resource!',
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {

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
        });

        $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip()
    </script>
    @foreach($table->getJs() as $asset)
        <script src="{{ $asset }}"></script>
    @endforeach
@endpush

@push('styles')
    <!-- DataTables -->
    <link href="/laradium/admin/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- Responsive datatable examples -->
    <link href="/laradium/admin/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.0/css/rowReorder.dataTables.min.css">
    @foreach($table->getCss() as $asset)
        <link href="{{ $asset }}" rel="stylesheet" type="text/css"/>
    @endforeach
@endpush