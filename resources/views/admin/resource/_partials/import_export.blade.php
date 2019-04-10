@if (method_exists($resource, 'import') || method_exists($resource, 'export'))
    @if (method_exists($resource, 'import'))
        <div>
            <form action="{{ route('admin.' . $resource->getResource()->getSlug() . '.import') }}" class="form-inline import-form" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="file" name="import" class="form-control d-none">
                <button type="button" class="btn btn-sm btn-info import-button">
                    <i class="fa fa-cloud-upload"></i> Import
                </button>
            </form>
        </div>
    @endif

    @if (method_exists($resource, 'export'))
        <div>
            <a href="{{ route('admin.' . $resource->getResource()->getSlug() . '.export') }}" class="btn btn-success btn-sm">
                <i class="fa fa-cloud-download"></i> Export
            </a>
        </div>
    @endif
@endif