@extends($layout->get(), ['title' => $resource->getBaseResource()->getName()])

@section('content')
    {!! $builder->render() !!}
@endsection