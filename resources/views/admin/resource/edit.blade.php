@extends($layout->get(), ['title' => 'Edit ' . $resource->getBaseResource()->getName()])

@section('content')
    @include('laradium::admin._partials.breadcrumbs', [
        'breadcrumbs' => $resource->getBreadcrumbs('edit')
    ])

    {!! $builder->render() !!}

@endsection