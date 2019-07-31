<template>
    <div>
        <div class="form-group">
            <label>
                {{ field.label }}
                <span v-if="field.info">
                    <i class="fa fa-info-circle" v-tooltip:top="field.info"></i>
                </span>
                <span class="badge badge-primary" v-if="field.config.is_translatable">
					{{ language }}
				</span>
            </label>
            <input v-if="field.config.multiple" type="hidden" :name="field.name + '[' + index + ']'" :value="value"
                   v-for="(value, index) in preSelected">
            <input v-else type="hidden" :value="selected" :name="field.name">
            <select2 :options="field.options" v-model="preSelected" :multiple="field.config.multiple">
                <option disabled value="0" v-text="placeholder"></option>
            </select2>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language'],

        data() {
            return {
                selected: null,
                preSelected: this.field.selected,
                placeholder: this.field.config.multiple ? 'Select multiple' : 'Select one'
            };
        },

        mounted() {
            let options = this.field.options;
            for (let option in options) {
                if (options[option].selected) {
                    this.selected = options[option].id;
                }
            }
        }
    }
</script>
