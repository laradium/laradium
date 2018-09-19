<div class="table-wrapper">
    <div class="table-responsive">
        <table class="resource-datatable table table-bordered" {{ isset($dataUrl) ? 'data-url="' . $dataUrl . '"' : '' }}>
            <thead>
            <tr>
                @if($table->isSortable())
                    <th>
                        #
                    </th>
                @endif
                @foreach($table->columns() as $column)
                    <th>{{ ucfirst(str_replace('_', ' ', $column['name'])) }}</th>
                @endforeach
                @if(!$table->columns()->where('column', 'action')->first())
                    <th>
                        Actions
                    </th>
                @endif
            </tr>
            </thead>
        </table>
    </div>
</div>