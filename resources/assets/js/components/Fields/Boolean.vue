<template>
    <div>
        <div class="form-group">
            <br>
            <template v-if="field.config.is_translatable" v-for="item in field.translations">
                <div v-show="language === item.iso_code"
                     class="checkbox checkbox-primary">
                    <input type="hidden" value="0" :name="item.name">
                    <input type="checkbox" value="1" v-model="item.checked" :name="item.name" :id="item.name"
                           v-bind="fieldAttributes">
                    <label :for="item.name">
                        {{ field.label }}
                    </label>
                    <span class="badge badge-primary"
                          v-if="field.config.is_translatable">
                        {{ language }}
                    </span>
                </div>
            </template>
            <template v-else>
                <div class="checkbox checkbox-primary">
                    <input type="hidden" value="0" :name="field.name">
                    <input type="checkbox" value="1" v-model="field.checked" :name="field.name" :id="field.name"
                           v-bind="fieldAttributes">
                    <label :for="field.name">
                        {{ field.label }}
                    </label>
                </div>
            </template>
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
