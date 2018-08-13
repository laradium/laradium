@if($field->isTranslatable())
    <div class="row">@endif
        <div class="col-md-2">
            <label for="{{ $field->getLabelId() }}">
                {{ $field->getLabel() }}
            </label>
        </div>
        @if($field->isTranslatable())
            @include('admin._partials.language-select')

            @foreach(config('translatable.locales') as $locale)
                <div class="js-tab js-tab-{{ $field->setLocale($locale) }} {{ $loop->iteration == 1? 'active' : 'hidden' }}">
                <textarea
                        name="{!! $field->getFieldNameAttribute() !!}"
                        class="form-control">{!! $field->getValue() !!}</textarea>
                </div>
            @endforeach
        @else
            <textarea
                    name="{!! $field->getFieldNameAttribute() !!}"
                    class="form-control">{!! $field->getValue() !!}</textarea>
@endif