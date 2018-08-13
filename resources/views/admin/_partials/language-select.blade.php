    <select name="translate_tab_select" class=" form-control-sm js-tab-select">
        @foreach(config('translatable.locales') as $locale)
            <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
        @endforeach
    </select>
