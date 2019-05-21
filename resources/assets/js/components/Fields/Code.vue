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
            <div v-for="(item, index) in field.translations">
                <input type="hidden" :name="item.name" :value="item.value" >
                <codemirror v-model="item.value" :key="index" :options="cmOptions" v-show="language === item.iso_code"></codemirror>
            </div>
        </div>
        <div v-else>
            <input type="hidden" :name="field.name" :value="field.value" >
            <codemirror v-model="field.value" :options="cmOptions"></codemirror>
        </div>
    </div>
</template>

<script>
    import { codemirror } from 'vue-codemirror'

    // language
    import 'codemirror/mode/xml/xml.js'
    import 'codemirror/mode/css/css.js'
    import 'codemirror/mode/javascript/javascript.js'

    import 'codemirror/lib/codemirror.css'
    // require active-line.js
    import'codemirror/addon/selection/active-line.js'
    // autoCloseTags
    import'codemirror/addon/edit/closetag.js'

    export default {
        props: ['field', 'language'],
        components: {
            codemirror
        },
        data(){
            return {
                cmOptions: null
            };
        },
        mounted() {
            let mode = 'text/' + this.field.style;
            this.cmOptions = {
                tabSize: 4,
                styleActiveLine: true,
                lineNumbers: true,
                line: true,
                mode: mode,
                lineWrapping: true,
                theme: 'default',
            };
        }
    }
</script>
