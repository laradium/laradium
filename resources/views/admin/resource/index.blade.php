@extends($layout->get(), ['title' => $resource->getBaseResource()->getName(), 'table' => $table])

@section('content')
    @include('laradium::admin._partials.breadcrumbs', [
        'breadcrumbs' => $resource->getBreadcrumbs('index')
    ])

    <div class="row">
        <div class="col-12">
            <div class="card-box">
                @include('laradium::admin._partials.messages')

                @if($resource->importHelper()->inProgress())
                    <div class="alert alert-info">
                        {{ $resource->importHelper()->status() }}
                    </div>
                @endif

                {!! $table->render() !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $layout->assetManager()->table()->js()->base() !!}

    {!! $table->config() !!}

    {!! $layout->assetManager()->table()->scripts() !!}

@endpush

@push('styles')
    {!! $layout->assetManager()->table()->css()->base() !!}
@endpush