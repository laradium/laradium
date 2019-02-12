<a href="#"
   class="js-editable"
   data-name="{{ $column['column_parsed'] }}"
   data-type="text"
   data-pk="{{ $item->id }}"
   data-url="{{ $slug }}/editable"
   data-title="Enter value">{{ $item->{$column['column_parsed']} }}</a>