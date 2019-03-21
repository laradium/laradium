@if($resource)
    @if($resource->hasAction('edit') && $resource->hasPermission('update'))
        <a href="/{{ $resource->isShared() ? '' : 'admin/' }}{{ $resource->getBaseResource()->getSlug() }}/{{ $item->id }}/edit"
           class="btn btn-primary btn-sm"><i class="mdi mdi-pencil"></i>
            Edit</a>
    @endif

    @if($resource->hasAction('delete') && $resource->hasPermission('delete'))
        <a href="javascript:;"
           data-url="/{{ $resource->isShared() ? '' : 'admin/' }}{{ $resource->getBaseResource()->getSlug() }}/{{ $item->id }}"
           class="btn btn-danger btn-sm js-delete-resource">
            <i class="mdi mdi-delete"></i> Delete
        </a>
    @endif
@endif