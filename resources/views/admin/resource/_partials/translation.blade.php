@if($item->translations->count())
    @foreach($item->translations as $translation)
        <li><b>{{ strtoupper($translation->locale) }}: </b>{{ $translation->{$column['column_parsed']} ?? 'Not set'}}
        </li>
    @endforeach
@else
    <span style="font-size:80%">- empty -</span>
@endif