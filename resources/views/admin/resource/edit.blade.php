@extends('aven::layouts.main', ['title' => 'Edit ' . $form->resourceName()])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                @include('aven::admin._partials.messages')

                {{--<form action="{{ $form->getAction('update') }}" method="post" id="crud-form">--}}
                {{--{{ method_field('PUT') }}--}}
                {{--{{ csrf_field() }}--}}
                <div id="crud-form">
                    <crud-form></crud-form>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary">Save</button>
                </div>
                {{--</form>--}}
            </div>
        </div>
    </div>
@endsection

@section('crud-url')
    <script>
        let url = '/admin/{{ $form->resourceName() }}/get-form/{{ $form->model()->id }}';
    </script>
@endsection