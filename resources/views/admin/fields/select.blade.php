<div class="form-group">
        <div class="row">
                <div class="col-md-2">
                        <label for="">{{ $field->name() }}</label>
                </div>
                <div class="col-md-10">
                        <select name="{!! $field->getNameAttribute() !!}"
                                class="form-control"
                        >
                                @foreach($field->getOptions() as $value => $name)
                                        <option value="{{ $value }}"
                                                {!! $field->getValue() == $value ? 'selected="selected"' : '' !!}>
                                                {{ $name }}
                                        </option>
                                @endforeach
                        </select>
                </div>

        </div>
</div>