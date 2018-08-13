@if($field->isTranslatable())
    <div class="form-group">
        <div class="row">
            <div class="col-md-2">
                {{ $field->name() }}
                @include('aven::admin._partials.language-select')
            </div>
            <div class="col-md-10">
                @foreach(config('translatable.locales') as $locale)
                    @php($field->setLocale($locale))
                    <div class="js-tab js-tab-{{ $locale }} {{ $loop->iteration == 1? 'active' : 'hidden' }}">
                        <input
                                type="text"
                                name="{!! $field->getNameAttribute() !!}"
                                value="{!! $field->getValue() !!}"
                                class="form-control">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    <div class="form-group">
        <div class="row">
            <div class="col-md-2">
                <label for="">{{ $field->name() }}</label>

            </div>
            <div class="col-md-10">
                <input
                        type="text"
                        name="{!! $field->getNameAttribute() !!}"
                        value="{!! $field->getValue() !!}"
                        class="form-control">
            </div>
        </div>
    </div>
@endif