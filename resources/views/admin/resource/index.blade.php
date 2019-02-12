@extends('laradium::layouts.main', ['title' => $resource->getBaseResource()->getName(), 'table' => $table])

@section('content')
    @include('laradium::admin._partials.breadcrumbs', [
        'items' => $resource->getBreadcrumbs('index')
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
    {!! $assetManager->table()->js()->base() !!}

    {!! $table->config() !!}

    {!! $assetManager->table()->scripts() !!}

@endpush

@push('styles')
    {!! $assetManager->table()->css()->base() !!}
@endpush