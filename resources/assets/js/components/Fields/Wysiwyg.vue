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
            <editor v-model="field.value" :init="config"></editor>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language'],
        data() {
            return {
                config: {
                    height: this.field.config.height ? this.field.config.height : 400,
                    plugins: 'preview autolink code fullscreen image link media table hr lists',
                    toolbar: 'formatselect | bold italic strikethrough forecolor backcolor | link image media | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat',
                    forced_root_block: '',
                    images_upload_url: this.field.config.upload_url,
                    automatic_uploads: true,
                    images_reuse_filename: true,
                    relative_urls: false,
                    remove_script_host: false,
                    convert_urls: true,
                    images_upload_handler: async (blobInfo, success, failure) => {
                        let form_data = new FormData();
                        form_data.append('file', blobInfo.blob(), blobInfo.filename());

                        try {
                            let request = await axios.post('/admin/attachments', form_data)

                            success(request.data.data.url);

                            return true;
                        } catch (error) {
                            failure(error.response.data.message);

                            return false;
                        }
                    },
                },
            }
        }
    }
</script>
