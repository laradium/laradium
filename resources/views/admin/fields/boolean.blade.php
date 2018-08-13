<div class="form-group">
    <div class="row">
        <div class="col-md-2">
            <label for="{{ $field->name() }}">{{ $field->name() }}</label>
        </div>
        <div class="col-md-8">
            <input type="hidden" name="{!! $field->getNameAttribute() !!}" value="0">
            <input
                    type="checkbox"
                    name="{!! $field->getNameAttribute() !!}"
                    id="{{ $field->name() }}"
                    @if($field->getValue() == 1)
                    checked="checked"
                    @endif
                    value="1"
            >
        </div>
    </div>
</div>