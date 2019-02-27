@if($item->file->exists())
    <div class="media">
        <div class="media-left my-auto pr-2">
            @if(is_image($item->file->url()))
                <a href="{{ extension_image($item->file->url()) }}" target="_blank">
                    <img src="{{ extension_image($item->file->url()) }}" class="media-object" style="width:70px">
                </a>
            @else
                <img src="{{ extension_image($item->file->url()) }}" class="media-object">
            @endif
        </div>
        <div class="media-body my-auto">
            @if(isset($locale))
                <span>
                    <strong>{{ $locale }}</strong>
                </span>
            @endif
            <span>{{ basename($item->file->url()) }}</span>
        </div>
    </div>
@else
    - empty -
@endif
