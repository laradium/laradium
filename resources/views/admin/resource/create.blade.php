@extends($layout->get(), ['title' => 'Create ' . $form->getResource()->getName()])

@section('content')
    @include('laradium::admin._partials.breadcrumbs', [
        'breadcrumbs' => $resource->getBreadcrumbs('index')
    ])

    <crud-form
            url="{{ $form->getAction('store') }}"></crud-form>
    <input type="hidden" name="data" value="{{ json_encode($form->data()) }}">

@endsection

@push('scripts')
    @foreach($js as $asset)
        <script src="{{ $asset }}"></script>
    @endforeach
@endpush

@push('styles')
    @foreach($css as $asset)
        <link href="{{ $asset }}" rel="stylesheet" type="text/css"/>
    @endforeach
@endpush