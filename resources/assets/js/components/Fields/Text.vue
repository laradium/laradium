<template>
    <div class="form-group">
        <label for="">
            <span v-html="field.label"></span>
            <span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
            <span class="badge badge-primary"
                  v-if="field.config.is_translatable">
                {{ language }}
            </span>
        </label>

        <div v-if="field.config.is_translatable">
            <input type="text"
                   v-model="item.value"
                   :name="item.name"
                   v-for="item in field.translations"
                   v-show="language === item.iso_code"
                   class="form-control" v-bind="attributes">
        </div>
        <div v-else>
            <input type="text" v-model="field.value" :name="field.name" class="form-control" v-bind="attributes">
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
    }
</script>
