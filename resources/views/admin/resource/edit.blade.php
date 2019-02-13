@extends($layout->get(), ['title' => 'Edit ' . $form->getResource()->getName()])

@section('content')
    @include('laradium::admin._partials.breadcrumbs', [
       'items' => $resource->getBreadcrumbs('edit')
   ])

    <crud-form url="{{ $form->getAction('update') }}"
               method="PUT"></crud-form>
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