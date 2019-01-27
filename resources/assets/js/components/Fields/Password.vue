<template>
    <div class="form-group">
        <label :for="field.name">
            {{ field.label }}
            <span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
        </label>

        <input type="password" :name="field.name" class="form-control" v-bind="fieldAttributes">
        <component :is="field.worker.type + '-field'"
                   :field="field.worker"
                   :language="language"
                   :replacement_ids="{}"
        ></component>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language'],
    }
</script>
