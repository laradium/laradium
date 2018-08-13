@extends('aven::layouts.main')
@section('content')
    <div class="col-md-6">

        <table class="table">
            <thead>
            @foreach($columns as $column)
                <th>{{ ucfirst(str_replace('_', ' ', $column['name'])) }}</th>
            @endforeach
            <th>Action</th>
            </thead>
            <tbody>
            @foreach ($resource as $item)
                <tr>
                    @foreach($columns as $column)
                        @if($column['type'] == 'text')
                            <td>{{ $item->{$column['name']} }}</td>

                        @elseif($column['type'] == 'boolean')
                            <td>{{ $item->{$column['name']} ? 'Yes' : 'No' }}</td>
                        @endif
                    @endforeach
                    <td><a href="/{{ str_plural($resourceName) }}/{{ $item->id }}/edit" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>
@endsection