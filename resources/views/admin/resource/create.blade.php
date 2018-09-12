@extends('laradium::layouts.main', ['title' => 'Create ' . $name])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <div id="crud-form">
                    <crud-form url="{{ $form->getAction('store') }}"></crud-form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('crud-url')
    <script>
        let url = '{{ route('admin.' . $slug . '.form') }}';
    </script>
@endsection