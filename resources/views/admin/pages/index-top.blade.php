<div class="row">
    <div class="col-md-2">
        Create new page
        <select v-model="selectedPage" class="form-control">
            @foreach($channels as $value => $channel)
                <option value="{{ $value }}">{{ $channel }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-10">
        <br>
        <button class="btn btn-primary" @click="redirectToCreatePage" :disabled="!selectedPage">
            <i class="fa fa-plus"></i> Create
        </button>
    </div>
</div>