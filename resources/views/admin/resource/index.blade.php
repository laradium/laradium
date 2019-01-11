@extends('laradium::layouts.main', ['title' => $resource->getBaseResource()->getName(), 'table' => $table])

@section('content')
    @include('laradium::admin._partials.breadcrumbs', [
        'items' => $resource->getBreadcrumbs('index')
    ])

    <div class="row">
        <div class="col-12">
            <div class="card-box">
                @include('laradium::admin._partials.messages')

                @if($resource->importHelper()->inProgress())
                    <div class="alert alert-info">
                        {{ $resource->importHelper()->status() }}
                    </div>
                @endif

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
                            @foreach($tabs as $id => $tabName)
                                <li class="nav-item">
                                    <a href="#tab-{{ getTabId($id) }}" data-toggle="tab" aria-expanded="false"
                                       class="nav-link {{ $loop->first ? 'active' : '' }}">
                                        {{ $tabName }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach

                    @foreach($table->getTabs() as $key => $tabs)
                        <div class="tab-content">
                            @foreach ($tabs as $id => $tabName)
                                <div role="tabpanel" class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                     id="tab-{{ getTabId($id) }}">

                                    @include('laradium::admin.resource._partials.table', [
                                        'dataUrl' => url('/admin/' . $resource->getBaseResource()->getSlug() . '/data-table?' . $key . '=' . $id)
                                    ])

                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    @include('laradium::admin.resource._partials.table')
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Required datatable js -->
    <script src="/laradium/admin/assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/laradium/admin/assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Responsive examples -->
    <script src="/laradium/admin/assets/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="/laradium/admin/assets/plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script>
        $(function () {
            function switchUpdate() {
                var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

                elems.forEach(function (html) {
                    if (!$(html).data('switchery')) {
                        new Switchery(html, {
                            disabled: $(html).data('disabled') === 'yes' ? true : false
                        });
                    }
                });
            }

            $.fn.editable.defaults.mode = 'inline';
            $.fn.editableform.buttons =
                '<button type="submit" class="btn btn-success editable-submit btn-sm"><i class="fa fa-check"></i></button>' +
                '<button type="button" class="btn editable-cancel btn-mini btn-sm"><i class="fa fa-close"></i></button>';

                    @if ($table->getTabs())
            var onTabChange = function (activeTab) {
                    // Entries datatable
                    var selector = '.tab-pane.active .resource-datatable';
                    if ($.fn.DataTable.isDataTable(selector)) {
                        return;
                    }

                    var dataTable = $(selector).DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: $(selector).data('url'),
                        columns: {!! $table->getColumnConfig()->toJson() !!},
                        order: [{!! isset($table->getOrderBy()['key']) ? '['.$table->getOrderBy()['key'].', "'.$table->getOrderBy()['direction'].'"]' : '' !!}]
                    }).on('draw.dt', function () {
                        $('.js-editable').editable({
                            error: function (response, newValue) {
                                if (response.status !== 422) {
                                    return 'Something went wrong, please, try again later.';
                                }

                                return response.responseJSON.message;
                            }
                        });
                        $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip()

                        switchUpdate();
                    });
                };

            // When page loads, we initialize first tab
            var activeTab = $('.nav-tabs li.active:first');
            onTabChange(activeTab);

            // When tabs are clicked, we load info there
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var activeTab = $('.nav-tabs li.active:first');
                onTabChange(activeTab);
            });
                    @else
            var dataTable = $('.resource-datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '/admin/{{ $resource->getBaseResource()->getSlug() }}/data-table',
                    columns: {!! $table->getColumnConfig()->toJson() !!},
                    order: [{!! isset($table->getOrderBy()['key']) ? '['.$table->getOrderBy()['key'].', "'.$table->getOrderBy()['direction'].'"]' : '' !!}]
                }).on('draw.dt', function () {
                    $('.js-editable').editable({
                        error: function (response, newValue) {
                            if (response.status !== 422) {
                                return 'Something went wrong, please, try again later.';
                            }

                            return response.responseJSON.message;
                        }
                    });
                    $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip();

                    switchUpdate();
                });
            @endif

            $(document).on('click', '.js-delete-resource', function (e) {
                e.preventDefault();
                var url = $(this).data('url');

                swal({
                    title: 'Are you sure?',
                    text: 'Once deleted, you will not be able to recover this resource!',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                })
                    .then(function (result) {
                        if (result.value) {

                            $.ajax({
                                type: 'POST',
                                url: url,
                                data: {
                                    _method: 'delete',
                                }
                            });

                            dataTable.ajax.reload(); // TODO: Fix this for multiple dts

                            swal('Item has been deleted!', {
                                icon: "success",
                            });
                        }
                    });
            });

            $(document).on('change', '.js-switch', function () {
                var id = $(this).data('id');
                var column = $(this).attr('name');

                $.post('{{ url('/admin/' . $resource->getBaseResource()->getSlug() . '/toggle') }}/' + id, {
                    column: column
                }, function () {
                    try {
                        toastr.success('Resource successfully updated');
                    } catch (e) {
                        //do nothing
                    }
                })
            });
        });

        $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip()
    </script>
    @foreach($table->getJs() as $asset)
        <script src="{{ $asset }}"></script>
    @endforeach
@endpush

@push('styles')
    <!-- DataTables -->
    <link href="/laradium/admin/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- Responsive datatable examples -->
    <link href="/laradium/admin/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    @foreach($table->getCss() as $asset)
        <link href="{{ $asset }}" rel="stylesheet" type="text/css"/>
    @endforeach
@endpush