@extends($layout->get(), ['title' => 'System', 'table' => $table])

@section('content')
    @if(config('laradium-system.server-info.enabled'))
        <div class="row">
            @if(config('laradium-system.server-info.cpu'))
                <div class="col">
                    <div class="card-box text-center">
                        <h3>CPU</h3>
                        <div class="box">
                            <div class="mask" style="margin:0 auto">
                                <div class="semi-circle"></div>
                                <div class="semi-circle--mask"
                                     style="transform: rotate({{ $systemInfo->cpu * 1.8 }}deg)"></div>
                                <div class="info-container">
                                    <div class="info">
                                        {{ $systemInfo->cpu }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(config('laradium-system.server-info.ram'))
                <div class="col">
                    <div class="card-box text-center">
                        <h3>Memory</h3>
                        <div class="box">
                            <div class="mask" style="margin:0 auto">
                                <div class="semi-circle"></div>
                                <div class="semi-circle--mask"
                                     style="transform: rotate({{ $systemInfo->ram->percent * 1.8 }}deg)"></div>
                                <div class="info-container">
                                    <div class="info">
                                        {{ $systemInfo->ram->percent }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(config('laradium-system.server-info.disk'))
                <div class="col">
                    <div class="card-box text-center">
                        <h3>Disk</h3>
                        <div class="box">
                            <div class="mask" style="margin:0 auto">
                                <div class="semi-circle"></div>
                                <div class="semi-circle--mask"
                                     style="transform: rotate({{ $systemInfo->disk->percent * 1.8 }}deg)"></div>
                                <div class="info-container">
                                    <div class="info">
                                        {{ $systemInfo->disk->percent }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card-box">
                @include('laradium::admin._partials.messages')

                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a href="#system-logs" data-toggle="tab" aria-expanded="false" class="nav-link active show">
                            Log
                        </a>
                    </li>

                    @if(config('laradium-system.php-info'))
                        <li class="nav-item">
                            <a href="#php-info" data-toggle="tab" aria-expanded="true" class="nav-link show">
                                PHP Info
                            </a>
                        </li>
                    @endif

                    @if(config('laradium-system.log-files'))
                        <li class="nav-item">
                            <a href="#downloads" data-toggle="tab" aria-expanded="false" class="nav-link show">
                                Log Files
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active show" id="system-logs">
                        {!! $table->render() !!}
                    </div>

                    @if(config('laradium-system.php-info'))
                        <div role="tabpanel" class="tab-pane fade" id="php-info">
                            <iframe src="{{ route('admin.system.phpInfo') }}"
                                    style="width:100%;height:1000px;border: 0"></iframe>
                        </div>
                    @endif

                    @if(config('laradium-system.log-files'))
                        <div role="tabpanel" class="tab-pane fade" id="downloads">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($logFiles as $logFile)
                                    <tr>
                                        <td>
                                            {{$logFile}}
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.system.download', $logFile) }}"
                                               class="btn btn-primary">
                                                <i class="fa fa-download"></i> Download
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="view-data" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="json-renderer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $layout->assetManager()->table()->js()->base() !!}

    {!! $table->config() !!}

    {!! $layout->assetManager()->table()->scripts() !!}

    <script src="{{ asset('laradium/admin/assets/plugins/json-viewer/jquery.json-viewer.js') }}"></script>

    <script>
        jQuery(document).on('click', '.view-data', function (e) {
            e.preventDefault();
            var self = jQuery(this);
            var id = self.data('id');
            var text = self.html();

            self.html('<i class="fa fa-refresh fa-spin"></i>');
            self.attr('disabled', true);

            jQuery.get('/admin/system/' + id + '/data', function (response) {
                jQuery('#json-renderer').jsonViewer(response.data);
                jQuery('#view-data').modal('show');
                self.html(text);
                self.attr('disabled', false);
            })
        });
    </script>
@endpush

@push('styles')
    {!! $layout->assetManager()->table()->css()->base() !!}

    <link rel="stylesheet" href="{{ asset('laradium/admin/assets/plugins/json-viewer/jquery.json-viewer.css') }}">
@endpush
