<template>
    <div>
        <div class="form-group">
            <h3>
                {{ field.label }}
            </h3>
            <div class="row">
                <input type="hidden" :value="field.value" :name="field.name + '[crud_worker]'">
                <div :class="'col-' + field.config.field_col.type + '-' + field.config.field_col.size" v-for="item in field.items">
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" :checked="item.is_checked" :id="field.name + '_' + item.id"
                               :name="field.name + '[]'" :value="item.id">
                        <label :for="field.name + '_' + item.id">{{ item.name }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language'],
    }
</script>
