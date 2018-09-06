<div class="table-wrapper">
    <div class="table-responsive">
        <table class="resource-datatable table table-bordered" <?php echo isset( $dataUrl ) ? 'data-url="' . $dataUrl . '"' : '' ?> >
            <thead>
            <tr>
                @foreach($table->columns() as $column)
                    <th>{{ ucfirst(str_replace('_', ' ', $column['name'])) }}</th>
                @endforeach
                <th>
                    Actions
                </th>
            </tr>
            </thead>
        </table>
    </div>
</div>