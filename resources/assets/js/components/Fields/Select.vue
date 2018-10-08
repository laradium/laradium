<template>
    <div>
        <div class="form-group" v-if="!input.isHidden">
            <label :for="input.name">
                {{ input.label }}
            </label>
            <select v-model="input.value" :name="input.name" :id="input.name" class="form-control" v-bind="attributes" @change="onChange()">
                <option
                        :value="option.value"
                        :selected="option.selected"
                        v-for="option in input.options"
                        :disabled="option.value === ''">
                    {{ option.text }}
                </option>
            </select>
        </div>
        <div v-if="input.isHidden">
            <input type="hidden" :name="input.name" :value="input.default">
        </div>
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],

        computed: {
            attributes() {
                return this.input.attr;
            }
        },

        created() {
            if (!this.input.onChange || this.input.value === '') {
                return;
            }

            this.onChange();
        },

        methods: {
            onChange() {
                let self = this;
                if (!this.input.onChange) {
                    return;
                }

                let fields = this.input.onChange.fields[this.input.value];
                if (fields) {
                    $.each(fields, function (index, field) {
                        self.$eventHub.$emit('change-input', field);
                    })
                }

                let languages = this.input.onChange.languages[this.input.value];
                if (languages) {
                    self.$eventHub.$emit('change-languages', languages);
                }
            }
        }
    }
</script>
