@foreach(translate()->languages() as $language)
    @php
        $translation = $item->translations->where('locale', $language->iso_code)->first();
    @endphp
    <li>
        <b>{{ strtoupper($language->iso_code) }}: </b>
        <a href="#"
           class="js-editable"
           data-name="{{ $column['column_parsed'] }}"
           data-type="text"
           data-pk="{{ $item->id }}"
           data-url="/admin/{{ $slug }}/editable/{{ $language->iso_code }}"
           data-title="Enter value">{{ $translation->{$column['column_parsed']} ?? '' }}</a>
    </li>
@endforeach