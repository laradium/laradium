@extends('aven::layouts.main', ['title' => ucfirst($model->getTable())])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                @include('aven::admin._partials.messages')
                @if($table->hasAction('create'))
                    <div class="pull-right">
                        <a href="/admin/{{ str_replace('_', '-', $model->getTable()) }}/create" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Create
                        </a>
                    </div>
                    @if(!$table->getAdditionalView())
                        <br><br>
                    @endif
                @endif
                @if($table->getAdditionalView())
                    <div class="row">
                        <div class="col-md-12">
                            {!! view($table->getAdditionalView(), $table->getAdditionalViewData() )->render() !!}
                        </div>
                    </div>
                    <br>
                @endif
                @if (method_exists($resource, 'import'))
                    @include('aven::admin.resource._partials.import')
                @endif
                <table class="resource-datatable table table-bordered">
                    <thead>
                    <tr>
                        @foreach($table->columns() as $column)
                            <th>{{ ucfirst(str_replace('_', ' ', $column['name'])) }}</th>
                        @endforeach
                        <th>
                            Actions
                        </th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Required datatable js -->
    <script src="/aven/admin/assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/aven/admin/assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Responsive examples -->
    <script src="/aven/admin/assets/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="/aven/admin/assets/plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script>
        $(function () {
            $.fn.editable.defaults.mode = 'inline';
            $.fn.editableform.buttons =
                '<button type="submit" class="btn btn-success editable-submit btn-sm"><i class="fa fa-check"></i></button>' +
                '<button type="button" class="btn editable-cancel btn-mini btn-sm"><i class="fa fa-close"></i></button>';

            let dataTable = $('.resource-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/admin/{{ $table->model()->getTable() }}/data-table',
                columns: [
                        @foreach($table->columns() as $column)
                    {
                        data: "{{ $column['column'] }}",
                        name: "{{ $column['translatable'] ? 'translations.'.$column['column'] : $column['column'] }}",
                    },
                        @endforeach
                    {
                        data: "action",
                        name: "action",
                        searchable: false,
                        orderable: false
                    },

                ]
            }).on('draw.dt', function () {
                $('.js-editable').editable({});
            });

            $(document).on('click', '.js-delete-resource', function (e) {
                e.preventDefault();
                let url = $(this).data('url');
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this resource!",
                    icon: "warning",
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

                            dataTable.ajax.reload();

                            swal("Item has been deleted!", {
                                icon: "success",
                            });
                        }
                    });
            });
        });
    </script>
@endpush

@push('styles')
    <!-- DataTables -->
    <link href="/aven/admin/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <!-- Responsive datatable examples -->
    <link href="/aven/admin/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
@endpush