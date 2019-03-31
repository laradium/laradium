@if($item->type === 'debug')
    <label class="badge badge-info badge-pill">{{ ucfirst($item->type) }}</label>
@elseif($item->type === 'warning')
    <label class="badge badge-warning badge-pill">{{ ucfirst($item->type) }}</label>
@elseif($item->type === 'error')
    <label class="badge badge-danger badge-pill">{{ ucfirst($item->type) }}</label>
@else
    <label class="badge badge-pill">{{ ucfirst($item->type) }}</label>
@endif
