@extends($layout->get(), ['title' => $title])

@section('content')
    @include('laradium::admin._partials.breadcrumbs', [
        'breadcrumbs' => $breadcrumbs
    ])

    {!! $form->render() !!}
@endsection

{{--@push('scripts')--}}
    {{--@foreach($js as $asset)--}}
        {{--<script src="{{ $asset }}"></script>--}}
    {{--@endforeach--}}
{{--@endpush--}}

{{--@push('styles')--}}
    {{--@foreach($css as $asset)--}}
        {{--<link href="{{ $asset }}" rel="stylesheet" type="text/css"/>--}}
    {{--@endforeach--}}
{{--@endpush--}}