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

                    @include('laradium::admin.table._partials.table', [
                        'dataUrl' => url($table->getSlug() . '/data-table?' . $key . '=' . $id)
                    ])

                </div>
            @endforeach
        </div>
    @endforeach
@else
    @include('laradium::admin.table._partials.table')
@endif