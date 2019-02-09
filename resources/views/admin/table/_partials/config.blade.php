<script>
    datatable_config = {
        id: '{{ $table->getResourceId() }}',
        columns: {!! $table->getColumnConfig()->toJson() !!},
        order: [{!! isset($table->getOrderBy()['key']) ? '['.$table->getOrderBy()['key'].', "'.$table->getOrderBy()['direction'].'"]' : '' !!}],
        slug: '{{ $table->getSlug() }}',
        has_tabs: false
    };
@if ($table->getTabs())
    datatable_config.selector = '.tab-pane.active .{{ $table->getResourceId() }}';
    datatable_config.has_tabs = true;
@else
    datatable_config.selector = '.' + datatable_config.id;
@endif

    datatables.push(datatable_config);
</script>