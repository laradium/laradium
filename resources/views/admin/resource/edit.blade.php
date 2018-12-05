@extends('laradium::layouts.main', ['title' => 'Edit ' . $form->getResource()->getName()])

@section('content')
    @include('laradium::admin._partials.breadcrumbs', [
       'items' => $resource->getBreadcrumbs('edit')
   ])

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <crud-form url="{{ $form->getAction('update') . '?' . http_build_query(request()->all()) }}"
                           method="PUT"></crud-form>
                <input type="hidden" name="data" value="{{ json_encode($form->data()) }}">
            </div>
        </div>
    </div>
@endsection