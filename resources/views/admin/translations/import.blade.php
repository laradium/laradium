@include('aven::admin._partials.messages')
<div class="row">
    <div class="col-md-6">

        <form action="{{ route('admin.translations.import') }}" class="form-horizontal" method="POST"
              enctype="multipart/form-data">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="row">
                <div class="col-md-6">
                    <input type="file" name="excel" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <button
                            type="submit"
                            class="btn btn-sm btn-info"
                    >Import
                    </button>
                    or
                    <a href="{{ route('admin.translations.export') }}" download class="btn btn-success btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </form>

    </div>

</div>
