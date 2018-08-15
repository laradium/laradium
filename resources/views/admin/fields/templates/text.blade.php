<div id="field-template-text" style="display: none;">
    <div class="js-text-translatable">
        <div class="form-group">
            <div class="row">
                <div class="col-md-2">
                    <label for="">__FIELD_NAME__</label>
                    @include('aven::admin._partials.language-select')
                </div>
                <div class="col-md-10">
                    @foreach(translate()->languages() as $language)
                        <div class="js-tab js-tab-{{ $language['iso_code'] }} {{ $loop->iteration == 1? 'active' : 'hidden' }}">
                            <input
                                    type="text"
                                    name="__FIELD_NAME_ATTRIBUTE__"
                                    value=""
                                    data-locale="{{ $language['iso_code'] }}"
                                    class="form-control js-field">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="js-text-non-translatable">
        <div class="form-group">
            <div class="row">
                <div class="col-md-2">
                    <label for="">__FIELD_NAME__</label>
                </div>
                <div class="col-md-10">
                    <input
                            type="text"
                            name="__FIELD_NAME_ATTRIBUTE__"
                            value=""
                            class="form-control">
                </div>
            </div>

        </div>
    </div>
</div>