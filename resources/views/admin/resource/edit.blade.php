@extends($layout->get(), ['title' => 'Edit ' . $resource->getBaseResource()->getName()])

@section('content')
    {!! $builder->render() !!}
@endsection