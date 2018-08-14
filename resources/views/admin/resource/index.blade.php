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
            $('.resource-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/{{ $table->model()->getTable() }}/data-table',
                columns: [
                    @foreach($table->columns() as $column)
                        {data: "{{ $column['column_parsed'] }}", name: "{{ $column['column_parsed'] }}", @if($column['relation'] == 'translations') searchable: false, orderable: false @endif},
                    @endforeach
                ]
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
@endpush