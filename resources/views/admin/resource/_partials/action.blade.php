@if($resource->hasAction('edit'))
    <a href="/admin/{{ $slug }}/{{ $item->id }}/edit" class="btn btn-primary btn-sm"><i class="mdi mdi-pencil"></i> Edit</a>
@endif

@if($resource->hasAction('delete'))
    <a href="javascript:;"
       data-url="/admin/{{ $slug }}/{{ $item->id }}"
       class="btn btn-danger btn-sm js-delete-resource">
        <i class="mdi mdi-delete"></i> Delete
    </a>
@endif