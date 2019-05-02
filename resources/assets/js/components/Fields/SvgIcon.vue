<template>
    <div>
        <div class="form-group">
            <label for="">
                {{ field.label }}
                <span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
                <span class="badge badge-primary"
                      v-if="field.config.is_translatable">
					{{ language }}
				</span>
            </label>
            <input type="hidden" :value="selected" :name="field.name">
            <select2 :options="field.options" v-model="selected">
                <option disabled value="0">Select one</option>
            </select2>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language', 'item'],

        data() {
            return {
                selected: null,
                config: {
                    data: this.field.options,
                    placeholder: 'Select',
                    width: '100%',
                    height: '100px',
                    escapeMarkup: (markup) => {
                        return markup;
                    },
                }
            };
        },

        created() {
            let options = this.field.options;
            for (let option in options) {
                if (options[option].id == this.field.value) {
                    this.selected = options[option].id;
                }
            }
        },

        computed: {
            attributes() {
                return this.field.attr;
            }
        }
    }
</script>
