<div class="js-has-many-list">
    @if($field->fieldGroups()->count())
        @foreach($field->fieldGroups()[0] as $group)
            <div class="form-group border js-block-{{ $group['id'] }}">
                <div class="col-md-12">
                    <h4>{{ ucfirst(str_singular($field->relationName())) }}

                        <div class="pull-right">
                            <button class="btn btn-danger btn-sm js-remove-has-many-item" data-id="{{ $group['id'] }}"
                                    data-url="/{{ $field->relationName() }}/{{ $group['id'] }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </h4>
                    @foreach($group['fields'] as $subField)
                        @if(isset($f['resource']))
                            <input type="hidden"
                                   name="{!! $f['resource']['nameAttribute'] !!}"
                                   value="{!! $f['resource']['value'] !!}"
                            >
                        @endif
                        {!! view($subField->view(), ['field' => $subField])->render() !!}
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>

<div class="form-group">
    <button class="btn btn-primary btn-sm js-add-hasMany">
        <i class="fa fa-plus"></i>
        Add {{ str_singular($field->relationName()) }}
    </button>
</div>

<div class="js-has-many-wrapper" style="display:none;">
    <div class="form-group border">
        <div class="col-md-12">
            <h4>{{ ucfirst(str_singular($field->relationName())) }}
                <div class="pull-right">
                    <button class="btn btn-danger btn-sm js-remove-has-many-item" data-id="__ITEM_ID__">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </h4>
            <div class="js-fields"></div>
        </div>
    </div>

</div>

@push('scripts')
    <script>
        let fieldList = JSON.parse('{!! $field->template() !!}');
    </script>
@endpush
