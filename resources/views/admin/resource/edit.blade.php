@extends('laradium::layouts.main', ['title' => 'Edit ' . str_replace('-', ' ', $form->resourceName())])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <div id="crud-form">
                    <crud-form url="{{ $form->getAction('update') }}" method="PUT"></crud-form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('crud-url')
    <script>
        let url = '{{ route('admin.' . $form->resourceName() . '.form', $form->model()->id) }}';
    </script>
@endsection