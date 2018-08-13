<label for="{{ $field->getLabelId() }}">{{ $field->getLabel() }}</label>
<select name="{!! $field->getFieldNameAttribute() !!}"
        id="{{ $field->getLabelId() }}"
        class="form-control"
>
    @foreach($options as $value => $name)
        <option value="{{ $value }}"
                {!! isset($resource) ? $resource->{$field->getFieldName()} == $value ? 'selected="selected"' : '' : '' !!}>
                {{ $name }}
        </option>
    @endforeach
</select>
