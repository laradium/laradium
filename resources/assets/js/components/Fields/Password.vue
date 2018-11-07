<template>
    <div class="form-group">
        <label :for="field.name">{{ field.label }}</label>

        <input type="password" :name="field.name" class="form-control" v-bind="attributes">
        <component :is="field.worker.type + '-field'"
                   :field="field.worker"
                   :language="language"
                   :replacement_ids="{}"
        ></component>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language', 'item'],

        computed: {
            attributes() {
                return this.field.attr;
            }
        }
    }
</script>
