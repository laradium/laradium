@extends('laradium::layouts.main', ['title' => 'Create ' . $form->getResource()->getName()])

@section('content')
    @include('laradium::admin._partials.breadcrumbs', [
       'items' => $resource->getBreadcrumbs('create')
    ])

    <crud-form
            url="{{ $form->getAction('store') . '?' . http_build_query(request()->all())  }}"></crud-form>
    <input type="hidden" name="data" value="{{ json_encode($form->data()) }}">

@endsection