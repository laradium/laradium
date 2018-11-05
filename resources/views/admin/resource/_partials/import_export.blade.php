@if (method_exists($resource, 'import') || method_exists($resource, 'export'))
    <div class="row">
        <div class="col-md-6">
            @if (method_exists($resource, 'import'))
                <form action="{{ route('admin.' . $resource->getBaseResource()->getSlug() . '.import') }}"
                      class="form-horizontal"
                      method="POST"
                      enctype="multipart/form-data">

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="row">
                        <div class="col-md-6">
                            <input type="file" name="import" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <button
                                    type="submit"
                                    class="btn btn-md btn-info"
                            >Import
                            </button>
                        </div>
                    </div>
                </form>
            @endif
            @if (method_exists($resource, 'export'))
                or
                <a href="{{ route('admin.' . $resource->getBaseResource()->getSlug() . '.export') }}"
                   class="btn btn-success btn-sm">
                    Export
                </a>
            @endif
        </div>
    </div>
    <hr>
@endif