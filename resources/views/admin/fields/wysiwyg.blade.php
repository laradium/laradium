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
                        <textarea name="{!! $field->getNameAttribute() !!}"
                                  class="form-control summer-note"
                        >
                            {!! $field->getValue() !!}
                        </textarea>
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
                <textarea name="{!! $field->getNameAttribute() !!}"
                          class="form-control summer-note"
                          >
                    {!! $field->getValue() !!}
                </textarea>
            </div>
        </div>
    </div>
@endif