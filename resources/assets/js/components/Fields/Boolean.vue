<template>
    <div>
        <div class="form-group">
            <br>
            <div class="checkbox checkbox-primary">
                <input type="hidden" value="0" :name="field.name">
                <input type="checkbox" value="1" v-model="field.checked" :name="field.name" :id="field.name" v-bind="fieldAttributes">
                <label :for="field.name">
                    {{ field.label }}
                </label>
            </div>
        </div>
        <div class="row" v-show="field.checked && field.fields.length">
            <div v-for="(newField, index) in field.fields" :class="newField.config.col">
                <component
                        :is="newField.type + '-field'"
                        :field="newField"
                        :language="language"
                        :key="index"
                ></component>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language'],
    }
</script>
