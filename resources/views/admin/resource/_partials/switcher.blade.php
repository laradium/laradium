<input id="js-switch-{{ $row->id }}-{{ $column }}" type="checkbox" class="js-switch" name="{{ $column }}"
       data-id="{{ $row->id }}"
       data-disabled="{{ $disabled ? 'yes' : 'no' }}"
       @if($row->$column) checked @endif/>