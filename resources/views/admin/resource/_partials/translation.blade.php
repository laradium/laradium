@if ($belongsTo = laradium()->belongsTo())
    @foreach ($item->{$belongsTo->getRelation()}->languages as $language)
        <li>
            <b>{{ strtoupper($language->iso_code) }}: </b>
            {{ $item->translateOrNew($language->iso_code)->{$column['column_parsed']} ?? 'Not set'}}
        </li>
    @endforeach
@else
    @if($item->translations->count())
        @foreach($item->translations as $translation)
            <li>
                <b>{{ strtoupper($translation->locale) }}: </b>
                {{ $translation->{$column['column_parsed']} ?? 'Not set'}}
            </li>
        @endforeach
    @else
        <span style="font-size:80%">- empty -</span>
    @endif
@endif