<div class="table-wrapper">
    <div class="table-responsive">
        <table class="resource-datatable table table-bordered" {{ isset($dataUrl) ? 'data-url=' . $dataUrl . '' : '' }}>
            <thead>
            <tr>
                @foreach($table->columns() as $column)
                    <th {{ $column['column'] === 'action' ? 'style="width: 150px"' : '' }}>{{ ucfirst(str_replace('_', ' ', $column['name'])) }}</th>
                @endforeach
                @if(!$table->columns()->where('column', 'action')->first())
                    <th style="width: 150px">
                        Actions
                    </th>
                @endif
            </tr>
            </thead>
        </table>
    </div>
</div>