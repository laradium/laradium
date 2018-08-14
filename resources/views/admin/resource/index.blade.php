@extends('aven::layouts.main')
@section('content')
    <div class="col-md-6">

        <table class="resource-datatable">
            <thead>
            <tr>
                @foreach($table->columns() as $column)
                    <th>{{ $column['name'] }}</th>
                @endforeach
            </tr>
            </thead>
        </table>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(function () {
            $.fn.editable.defaults.mode = 'inline';
            $.fn.editableform.buttons =
                '<button type="submit" class="btn btn-success editable-submit btn-sm"><i class="fa fa-check"></i></button>' +
                '<button type="button" class="btn editable-cancel btn-mini btn-sm"><i class="fa fa-close"></i></button>';

            $('.resource-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/{{ $table->model()->getTable() }}/data-table',
                columns: [
                    @foreach($table->columns() as $column)
                        {data: "{{ $column['column_parsed'] }}", name: "{{ $column['column_parsed'] }}", @if($column['relation'] == 'translations') searchable: false, orderable: false @endif},
                    @endforeach
                ],
                initComplete: function () {
                    $('.js-editable').editable({});
                }
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
@endpush