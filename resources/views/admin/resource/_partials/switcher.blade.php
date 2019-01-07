<input id="js-switch-{{ $row->id }}-{{ $column }}" type="checkbox" class="js-switch" name="{{ $column }}"
       data-id="{{ $row->id }}"
       @if($row->$column) checked @endif/>

<script>
    var elem = document.querySelector('#js-switch-{{ $row->id }}-{{ $column }}');
    var init = new Switchery(elem, {
        disabled: {{ $disabled ? 'true' : 'false' }}
    });
</script>