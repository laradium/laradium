@extends('aven::layouts.main', ['title' => 'Edit ' . $form->resourceName()])
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
        let url = '/admin/{{ $form->resourceName() }}/get-form/{{ $form->model()->id }}';
    </script>
@endsection