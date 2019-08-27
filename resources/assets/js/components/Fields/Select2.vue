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
            <input type="hidden" :value="selected" :name="field.name">
            <select2 :options="field.options" :config="field.config" v-model="selected">
                <option disabled value="0">Select one</option>
            </select2>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language'],

        data() {
            return {
                selected: null
            };
        },

        mounted() {
            this.selected = this.field.value;
            let options = this.field.options;
            for (let option in options) {
                if (!options.hasOwnProperty(option)) {
                    continue;
                }
                if (options[option].selected) {
                    this.selected = options[option].id;
                }
            }
        }
    }
</script>
