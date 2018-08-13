<div class="form-group">
    <label for="{{ $field->getLabelId() }}">
        <input type="hidden" name="{!! $field->getFieldNameAttribute() !!}" value="0">
        <input
                type="checkbox"
                name="{!! $field->getFieldNameAttribute() !!}"
                id="{{ $field->getLabelId() }}"
                @if($field->getValue() == 1)
                    checked="checked"
                @endif
                value="1"
        >
        {{ $field->getLabel() }}
    </label>
</div>