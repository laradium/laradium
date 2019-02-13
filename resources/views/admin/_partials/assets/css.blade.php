@foreach($assets as $asset)
    <link rel="stylesheet" href="{{ $asset }}">
@endforeach

<meta name="csrf-token" content="{{ csrf_token() }}">

@include('laradium::admin._partials.variables')