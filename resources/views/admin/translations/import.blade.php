@include('aven::admin._partials.messages')
    <div class="row">
        <div class="col-md-6">

            <form action="{{ route('admin.translations.import') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <input type="file" name="excel" class="trans-in-db-inline-block" required>

                <button
                        type="submit"
                        class="btn btn-xs btn-info"
                >Import translations</button>
                or
                <a href="{{ route('admin.translations.export') }}" download class="btn btn-success btn-xs">
                    Export translations
                </a>
            </form>

        </div>

    </div>
