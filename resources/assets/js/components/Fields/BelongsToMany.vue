<template>
    <div>
        <div class="form-group">
            <h3>
                {{ field.label }}
                <span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
            </h3>

            <input type="hidden" :value="field.value" :name="field.name + '[crud_worker]'">

            <div class="row" v-if="field.config.render_as_tags">
                <div class="col-md-12">
                    <input type="hidden" :name="field.name + '[' + index + ']'" :value="value"
                           v-for="(value, index) in preSelected">
                    <select2 :options="field.options" v-model="preSelected" multiple="true">
                        <option disabled value="0">Select one</option>
                    </select2>
                </div>
            </div>

            <div class="row" v-else>
                <div :class="'col-' + field.config.field_col.type + '-' + field.config.field_col.size"
                     v-for="item in field.items">
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" :checked="item.is_checked" :id="field.name + '_' + item.id"
                               :name="field.name + '[]'" :value="item.id">
                        <label :for="field.name + '_' + item.id">{{ item.name }}</label>
                    </div>
                    <div v-if="item.fields" class="row">
                        <div v-for="(pivotField, index) in item.fields" :class="pivotField.config.col">
                            <component
                                    :is="pivotField.type + '-field'"
                                    :field="pivotField"
                                    :language="language"
                                    :key="index"
                            ></component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language'],
        data() {
            return {
                preSelected: this.field.selected,
            };
        }
    }
</script>
