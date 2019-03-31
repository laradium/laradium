@if($resource)
    <button type="button" class="btn btn-primary btn-sm view-data" data-id="{{ $item->id }}">
        <i class="fa fa-search"></i> View Data
    </button>

    @if($resource->hasAction('delete') && $resource->hasPermission('delete'))
        <a href="javascript:;"
           data-url="/{{ $resource->isShared() ? '' : 'admin/' }}{{ $resource->getBaseResource()->getSlug() }}/{{ $item->id }}"
           class="btn btn-danger btn-sm js-delete-resource">
            <i class="mdi mdi-delete"></i> Delete
        </a>
    @endif
@endif
