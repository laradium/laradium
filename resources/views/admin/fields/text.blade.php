@if($field->isTranslatable())
    <div class="form-group">
        <div class="row">
            <div class="col-md-2">
                {{ $field->name() }}
                @include('aven::admin._partials.language-select')
            </div>
            <div class="col-md-10">
                @foreach(translate()->languages() as $language)
                    @php($field->setLocale($language['iso_code']))
                    <div class="js-tab js-tab-{{ $language['iso_code'] }} {{ $loop->iteration == 1? 'active' : 'hidden' }}">
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