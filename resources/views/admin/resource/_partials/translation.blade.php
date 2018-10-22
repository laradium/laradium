@if ($belongsTo = laradium()->belongsTo())
    @php
        $languages = $item->{$belongsTo->getRelation()}->languages ?? \App\Models\Language::whereNull($belongsTo->getForeignKey())->get();
    @endphp
    @foreach ($languages as $language)
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