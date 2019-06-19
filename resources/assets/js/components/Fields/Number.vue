<template>
    <div class="form-group">
        <label>
            <span v-html="field.label"></span>
            <span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
            <span class="badge badge-primary"
                  v-if="field.config.is_translatable">
                {{ language }}
            </span>
        </label>

        <div v-if="field.config.is_translatable">
            <input type="number"
                   v-model="item.value"
                   :min="field.config.min"
                   :max="field.config.max"
                   :step="field.config.step"
                   :name="item.name"
                   v-for="item in field.translations"
                   v-show="language === item.iso_code"
                   class="form-control" v-bind="fieldAttributes">
        </div>
        <div v-else>
            <input type="number"
                   v-model="field.value"
                   :name="field.name"
                   :min="field.config.min"
                   :max="field.config.max"
                   :step="field.config.step"
                   class="form-control"
                   v-bind="fieldAttributes">
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language']
    }
</script>
