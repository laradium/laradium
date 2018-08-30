@if($table->hasAction('edit'))
    <a href="/admin/{{ str_replace('_', '-', $item->getTable()) }}/{{ $item->id }}/edit" class="btn btn-primary btn-sm">Edit</a>
@endif
@if($table->hasAction('delete'))
    <a href="javascript:;"
       data-url="/admin/{{ str_replace('_', '-', $item->getTable()) }}/{{ $item->id }}"
       class="btn btn-danger btn-sm js-delete-resource">
        Delete
    </a>
@endif