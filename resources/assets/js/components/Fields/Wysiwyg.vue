<template>
    <div class="form-group">
        <label for="">
            {{ field.label }}
            <span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
            <span class="badge badge-primary"
                  v-if="field.config.is_translatable">
                {{ language }}
            </span>
        </label>
        <div v-if="field.config.is_translatable">

            <div v-for="(item, index) in field.translations"
                 v-show="language === item.iso_code">
                <input type="hidden"
                       :name="item.name"
                       v-model="item.value"
                >
                <editor v-model="item.value" :init="config" :key="index"></editor>
            </div>

        </div>
        <div v-else>
            <input type="hidden"
                   :name="field.name"
                   v-model="field.value"
            >
            <editor v-model="field.value" :init="config" :key="index"></editor>
        </div>
    </div>
</template>

<script>
    import Editor from '@tinymce/tinymce-vue';

    export default {
        props: ['field', 'language'],
        components: {
            'editor': Editor // <- Important part
        },
        data() {
            return {
                config: {
                    plugins: 'preview autolink code fullscreen image link media table hr lists',
                    toolbar: 'formatselect | bold italic strikethrough forecolor backcolor | link image media | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat',
                    forced_root_block: '',
                    images_upload_url: this.field.config.upload_url,
                    automatic_uploads: true,
                    images_reuse_filename: true,
                    relative_urls : false,
                    remove_script_host : false,
                    convert_urls : true,
                    images_upload_handler:  (blobInfo, success, failure) => {
                        let xhr, formData;

                        xhr = new XMLHttpRequest();
                        xhr.withCredentials = false;
                        xhr.open('POST', '/admin/attachments/upload');

                        xhr.onload = function() {
                            let json;

                            if (xhr.status != 200) {
                                failure('HTTP Error: ' + xhr.status);
                                return;
                            }

                            json = JSON.parse(xhr.responseText);

                            if (!json || typeof json.data.url != 'string') {
                                failure('Invalid JSON: ' + xhr.responseText);
                                return;
                            }

                            success(json.data.url);
                        };

                        formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());

                        xhr.send(formData);
                    },
                },
            }
        },
    }
</script>
