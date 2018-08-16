@extends('aven::layouts.main', ['title' => 'Create ' . $form->resourceName()])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                @include('aven::admin._partials.messages')
                {{--<form action="{{ $form->getAction('update') }}" method="post">--}}
                    {{--{{ csrf_field() }}--}}

                    <div id="crud-form">
                        <crud-form></crud-form>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">Create</button>
                    </div>
                {{--</form>--}}
            </div>
        </div>
    </div>
@endsection

@section('crud-url')
    <script>
        let url = '/admin/{{ $form->resourceName() }}/get-form';
    </script>
@endsection