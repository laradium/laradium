@extends('aven::layouts.main', ['title' => 'Create ' . $form->resourceName()])
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
        let url = '{{ route('admin.' . $form->resourceName() . '.form') }}';
    </script>
@endsection