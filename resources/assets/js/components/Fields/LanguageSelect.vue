<template>
    <div v-if="form.data.is_translatable && form.data.languages.length" class="form-group">
        <label>Language</label>
        <select class="form-control language-select" v-model="form.data.default_language">
            <option :value="language.iso_code" v-for="language in form.data.languages">
                {{ language.iso_code }}
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: this.getForm()
            }
        },

        methods: {
            getForm() {
                let component = null;
                let parent = this.$parent;
                while (parent && !component) {
                    if (parent.$options.name === 'CrudForm') {
                        component = parent
                    }
                    parent = parent.$parent
                }
                return component
            }
        }
    }
</script>