<template>
    <div class="form-group">
        <label for="">{{ field.label }}
            <span class="badge badge-primary"
                  v-if="field.config.is_translatable">
                {{ language }}
            </span>
        </label>
        <br>
        <div v-if="field.config.is_translatable">
            <div v-for="item in field.translations" v-show="language === item.iso_code" v-if="item.file.url">
                <a :href="item.file.url" target="_blank">
                    {{ item.file.file_name }} ({{ item.file.file_size }} kb)
                </a>
                <button class="btn btn-danger btn-sm"
                        @click.prevent="deleteFile(item, item.file.deleteUrl)">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            <input type="file" :name="item.name" class="form-control" v-for="item in field.translations"
                   v-show="language === item.iso_code" v-bind="attributes">
        </div>
        <div v-else>
            <div v-if="field.file.url">
                <a :href="field.file.url" target="_blank">
                    {{ field.file.file_name }} ({{ field.file.file_size }} kb)
                </a>
                <button class="btn btn-danger btn-sm"
                        @click.prevent="deleteFile(field, field.file.deleteUrl)">
                    <i class="fa fa-trash"></i>
                </button>
            </div>

            <input type="file" :name="field.name" class="form-control" v-bind="attributes">
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language', 'item'],

        computed: {
            attributes() {
                return this.field.attr;
            }
        },

        methods: {
            deleteFile(field, url) {
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                })
                    .then(function (result) {
                        if (result.value) {
                            axios({
                                method: 'DELETE',
                                url: url
                            }).then(res => {
                                field.file.url = null;

                                swal({
									type: 'success',
									title: 'File has been deleted!',
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
                    });
            }
        }
    }
</script>