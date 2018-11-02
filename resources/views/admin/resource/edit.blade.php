@extends('laradium::layouts.main', ['title' => 'Edit ' . $form->getResource()->getName()])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <div id="crud-form">
                    <crud-form url="{{ $form->getAction('update') }}" method="PUT"></crud-form>
                    <input type="hidden" name="data" value="{{ json_encode($form->data()) }}">
                </div>
            </div>
        </div>
    </div>
@endsection