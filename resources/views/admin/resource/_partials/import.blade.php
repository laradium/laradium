<div class="row">
    <div class="col-md-6">
        <form action="/admin/{{ str_replace('_', '-', $model->getTable()) }}/import" class="form-horizontal" method="POST"
              enctype="multipart/form-data">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="row">
                <div class="col-md-6">
                    <input type="file" name="import" class="form-control">
                </div>
                <div class="col-md-6">
                    <button
                            type="submit"
                            class="btn btn-md btn-info"
                    >Import
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>