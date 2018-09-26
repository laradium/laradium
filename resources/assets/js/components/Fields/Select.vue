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
                        v-for="option in input.options">
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

        methods: {
            onChange() {
                if (!this.input.onChange) {
                    return;
                }


            }
        }
    }
</script>
