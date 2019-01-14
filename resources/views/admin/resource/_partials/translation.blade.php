@foreach(translate()->languages() as $language)
    @php
        $translation = $item->translations->where('locale', $language->iso_code)->first();
    @endphp
    <li><b>{{ strtoupper($language->iso_code) }}: </b>{{ $translation->{$column['column_parsed']} ?? 'Not set' }}</li>
@endforeach