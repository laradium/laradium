@extends('laradium::layouts.main', ['title' => 'Create ' . $form->getResource()->getName()])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <div id="crud-form">
                    <crud-form url="{{ $form->getAction('store') }}"></crud-form>
                    <input type="hidden" name="data" value="{{ json_encode($form->data()) }}">
                </div>
            </div>
        </div>
    </div>
@endsection