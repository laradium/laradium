@extends('aven::layouts.main')
@section('content')
    <div class="col-md-6">
        <h2>Create {{ $form->resourceName() }}</h2>
        @include('aven::admin._partials.messages')
        <form action="{{ $form->getAction('update') }}" method="post">
            {{ csrf_field() }}

            @foreach ($form->fields() as $field)
                {!! view($field->view(), compact('field'))->render() !!}
            @endforeach

            <div class="form-group">
                <button class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
@endsection