@extends($layout->get(), ['title' => 'Create ' . $resource->getBaseResource()->getName()])

@section('content')
    {!! $builder->render() !!}
@endsection