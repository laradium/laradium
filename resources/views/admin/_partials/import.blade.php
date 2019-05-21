@if($resource->importHelper()->inProgress())
    <div class="alert alert-info">
        {{ $resource->importHelper()->status() }}
    </div>
@endif