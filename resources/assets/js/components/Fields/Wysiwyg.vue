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
                <trumbowyg v-model="item.value"
                           :key="index"
                           :config="config"
                ></trumbowyg>
            </div>

        </div>
        <div v-else>
            <input type="hidden"
                   :name="field.name"
                   v-model="field.value"
            >
            <trumbowyg v-model="field.value"
                       :config="config"
            ></trumbowyg>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language'],

        data() {
            return {
                config: {
                    removeformatPasted: true,
                    btnsDef: {
                        image: {
                            dropdown: ['insertImage', this.field.config.upload_url ? 'upload' : ''],
                            ico: 'insertImage'
                        }
                    },
                    btns: [
                        ['strong', 'em', 'del'],
                        ['superscript', 'subscript'],
                        ['fontsize', 'fontfamily'],
                        ['foreColor', 'backColor', 'lineheight'],
                        ['link'],
                        ['image'],
                        ['noembed'],
                        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                        ['unorderedList', 'orderedList'],
                        ['horizontalRule'],
                        ['table'],
                        ['formatting'],
                        ['undo', 'redo'],
                        ['removeformat'],
                        ['viewHTML'],
                        ['fullscreen']
                    ],
                    plugins: {
                        upload: {
                            serverPath: this.field.config.upload_url,
                            fileFieldName: 'image',
                            headers: {},
                            urlPropertyName: 'data.url'
                        }
                    }
                },
            }
        },
    }
</script>
