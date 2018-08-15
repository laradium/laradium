    <select name="translate_tab_select" class=" form-control-sm js-tab-select">
        @foreach(translate()->languages() as $language)
            <option value="{{ $language['iso_code'] }}">{{ strtoupper($language['iso_code']) }}</option>
        @endforeach
    </select>
