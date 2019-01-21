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
            <input type="email"
                   v-model="item.value"
                   :key="index"
                   v-for="(item, index) in field.translations"
                   v-show="language === item.iso_code"
                   class="form-control"
                   v-bind="fieldAttributes">
        </div>
        <div v-else>
            <input type="email" v-model="field.value" :name="field.name" class="form-control" v-bind="fieldAttributes">
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language']
    }
</script>
