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
            <textarea v-model="item.value"
                      :name="item.name"
                      v-for="item in field.translations"
                      v-show="language === item.iso_code"
                      class="form-control" v-bind="fieldAttributes"></textarea>
        </div>
        <div v-else>
            <textarea v-model="field.value" :name="field.name" class="form-control" v-bind="fieldAttributes"></textarea>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language']
    }
</script>
