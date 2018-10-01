<template>
    <div class="form-group">
        <label for="">{{ input.label }}
            <span class="badge badge-primary"
                  v-if="input.isTranslatable">
                {{ language }}
            </span>
        </label>
        <br>
        <div v-if="input.isTranslatable">
            <div v-for="item in input.translatedAttributes" v-show="language === item.iso_code" v-if="item.url">
                <a :href="item.url" target="_blank">
                    {{ item.file_name }} ({{ item.file_size }} kb)
                </a>
                <button class="btn btn-danger btn-sm"
                        @click.prevent="deleteFile(item, item.deleteUrl)">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            <input type="file" :name="item.name" class="form-control" v-for="item in input.translatedAttributes"
                   v-show="language === item.iso_code" v-bind="attributes">
        </div>
        <div v-else>
            <div v-if="input.url">
                <a :href="input.url" target="_blank">
                    {{ input.file_name }} ({{ input.file_size }} kb)
                </a>
                <button class="btn btn-danger btn-sm"
                        @click.prevent="deleteFile(input, input.deleteUrl)">
                    <i class="fa fa-trash"></i>
                </button>
            </div>

            <input type="file" :name="input.name" class="form-control" v-bind="attributes">
        </div>
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],

        computed: {
            attributes() {
                return this.input.attr;
            }
        },

        methods: {
            deleteFile(input, url) {
                swal({
                    title: 'Are you sure?',
                    text: 'Once deleted, you will not be able to recover this item!',
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            if (url) {
                                axios({
                                    method: 'DELETE',
                                    url: url
                                }).then(res => {

                                    input.url = null;

                                    swal('File has been deleted!', {
                                        icon: 'success',
                                    });

                                }).catch(res => {
                                    let error = document.createElement('div');
                                    error.innerHTML = 'Something went wrong! <br> If issue persists, please, contact technical staff.';

                                    swal({
                                        icon: 'error',
                                        title: 'Oops...',
                                        content: error
                                    })
                                });
                            }
                        }
                    });
            }
        }
    }
</script>