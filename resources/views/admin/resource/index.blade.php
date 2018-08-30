@extends('aven::layouts.main', ['title' => ucfirst(str_replace('_', ' ', $model->getTable())), 'table' => $table])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                @include('aven::admin._partials.messages')
                @include('aven::admin.resource._partials.import_export')

                @if($table->getAdditionalView())
                    <div class="row">
                        <div class="col-md-12">
                            {!! view($table->getAdditionalView(), $table->getAdditionalViewData() )->render() !!}
                        </div>
                    </div>
                    <hr>
                @endif

                @if ($table->getTabs())
                    @foreach($table->getTabs() as $key => $tabs)
                        <ul class="nav nav-tabs">
                            @foreach($tabs as $id => $name)
                                <li class="nav-item">
                                    <a href="#tab-{{ $id }}" data-toggle="tab" aria-expanded="false"
                                       class="nav-link {{ $loop->first ? 'active' : '' }}">
                                        {{ $name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach

                    @foreach($table->getTabs() as $key => $tabs)
                        <div class="tab-content">
                            @foreach ($tabs as $id => $name)
                                <div role="tabpanel" class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                     id="tab-{{ $id }}">
                                    <div class="table-wrapper">
                                        <div class="table-responsive">
                                            <table class="resource-datatable table table-bordered"
                                                   data-url="{{ url('/admin/' . $table->model()->getTable() . '/data-table?' . $key . '=' . $id) }}">
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
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    <div class="table-wrapper">
                        <div class="table-responsive">
                            <table class="resource-datatable table table-bordered">
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
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Required datatable js -->
    <script src="/aven/admin/assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/aven/admin/assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Responsive examples -->
    <script src="/aven/admin/assets/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="/aven/admin/assets/plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script>
        $(function () {
            $.fn.editable.defaults.mode = 'inline';
            $.fn.editableform.buttons =
                '<button type="submit" class="btn btn-success editable-submit btn-sm"><i class="fa fa-check"></i></button>' +
                '<button type="button" class="btn editable-cancel btn-mini btn-sm"><i class="fa fa-close"></i></button>';

                    @if ($table->getTabs())
            let onTabChange = function (activeTab) {

                    // Entries datatable
                    let selector = '.tab-pane.active .resource-datatable';
                    if ($.fn.DataTable.isDataTable(selector)) {
                        return;
                    }

                    let dataTable = $(selector).DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $(selector).data('url'),
                        columns: [
                                @foreach($table->columns() as $column)
                            {
                                data: "{{ $column['column'] }}",
                                name: "{{ $column['translatable'] ? 'translations.'.$column['column'] : $column['column'] }}",
                            },
                                @endforeach
                            {
                                data: "action",
                                name: "action",
                                searchable: false,
                                orderable: false
                            },

                        ]
                    }).on('draw.dt', function () {
                        $('.js-editable').editable({});
                    });
                };

            // When page loads, we initialize first tab
            let activeTab = $('.nav-tabs li.active:first');
            onTabChange(activeTab);

            // When tabs are clicked, we load info there
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                let activeTab = $('.nav-tabs li.active:first');
                onTabChange(activeTab);
            });
                    @else
            let dataTable = $('.resource-datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '/admin/{{ str_replace('_', '-', $table->model()->getTable()) }}/data-table',
                    columns: [
                            @foreach($table->columns() as $column)
                        {
                            data: "{{ $column['column'] }}",
                            name: "{{ $column['translatable'] ? 'translations.'.$column['column'] : $column['column'] }}",
                        },
                            @endforeach
                        {
                            data: "action",
                            name: "action",
                            searchable: false,
                            orderable: false
                        },

                    ]
                }).on('draw.dt', function () {
                    $('.js-editable').editable({});
                });
            @endif

            $(document).on('click', '.js-delete-resource', function (e) {
                e.preventDefault();
                let url = $(this).data('url');
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this resource!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {

                            $.ajax({
                                type: 'POST',
                                url: url,
                                data: {
                                    _method: 'delete',
                                }
                            });

                            dataTable.ajax.reload();

                            swal("Item has been deleted!", {
                                icon: "success",
                            });
                        }
                    });
            });
        });
    </script>
@endpush

@push('styles')
    <!-- DataTables -->
    <link href="/aven/admin/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <!-- Responsive datatable examples -->
    <link href="/aven/admin/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
@endpush