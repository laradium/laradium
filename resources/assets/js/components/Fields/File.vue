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
            <div v-for="(item, index) in field.translations" v-show="language === item.iso_code" v-if="item.file.url">
                <div v-if="item.file.is_deleted">
                    <input type="hidden" :name="item.name + '[remove]'" value="1">
                    <span><i>Deleted</i></span>

                    <button class="btn btn-primary btn-sm"
                            @click.prevent="restoreFile(item)">
                        <i class="fa fa-undo"></i>
                    </button>
                </div>
                <div v-else>
                    <a :href="item.file.url" target="_blank">
                        {{ item.file.file_name }} ({{ item.file.file_size }} kb)
                    </a>
                    <button class="btn btn-danger btn-sm"
                            @click.prevent="deleteFile(item)">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>

                <input type="file" :name="item.name" class="form-control" v-for="item in field.translations"
                       v-show="language === item.iso_code" v-bind="attributes">
            </div>
        </div>
        <div v-else>
            <div v-if="field.file.url">
                <div v-if="field.file.is_deleted">
                    <input type="hidden" :name="field.name + '[remove]'" value="1">
                    <span><i>Deleted</i></span>

                    <button class="btn btn-primary btn-sm"
                            @click.prevent="restoreFile(field)">
                        <i class="fa fa-undo"></i>
                    </button>
                </div>
                <div v-else>
                    <a :href="field.file.url" target="_blank">
                        {{ field.file.file_name }} ({{ field.file.file_size }} kb)
                    </a>
                    <button class="btn btn-danger btn-sm"
                            @click.prevent="deleteFile(field)">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
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

        data() {
            return {
                removed_files: {}
            };
        },

        methods: {
            deleteFile(item) {
                swal({
                    title: 'Are you sure?',
                    //text: 'Once deleted, you will not be able to recover this item!',
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            item.file.is_deleted = true;
                        }
                    });
            },

            restoreFile(item) {
                item.file.is_deleted = false;
            },
        }
    }
</script>