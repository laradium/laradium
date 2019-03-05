<template>
    <div class="row">
        <div :class="classes" v-bind="fieldAttributes">
            <button class="btn btn-primary mb-1" :disabled="form.isSubmitted">
                <span v-if="form.isSubmitted">
                    <i class="fa fa-cog fa-spin fa-fw"></i> Saving...
                </span>
                <span v-else>
                    <i class="fa fa-floppy-o"></i> Save
                </span>
            </button>

            <button class="btn btn-primary mb-1" @click.stop.prevent="form.onSubmit(form, form.data.actions.index)"
                    :disabled="form.isSubmitted" v-if="!form.isSubmitted">
                Save & Return
            </button>

            <component
                v-for="(field, index) in data.fields"
                :is="field.type + '-field'"
                :field="field"
                :data="field"
                :language="language"
                :replacement_ids="{}"
                :key="'save-button-field-' + index"
            ></component>
        </div>
        <div class="col-md-3"
             v-if="data.config.locale_selector && form.data.is_translatable && form.data.languages.length"
             v-bind="fieldAttributes">
            <div class="row">
                <div class="col-md-4 my-auto text-right">
                    <label>Language</label>
                </div>
                <div class="col-md-8">
                    <select class="form-control language-select" v-model="form.data.default_language">
                        <option :value="language.iso_code" v-for="language in form.data.languages">
                            {{ language.iso_code }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['data', 'language'],

        data() {
            return {
                form: this.getForm()
            }
        },

        computed: {
            classes() {
                let classes = '';

                if (this.data.config.locale_selector) {
                    classes += 'col-md-9 '
                } else {
                    classes += 'col-md-12 '
                }

                let attributes = this.fieldAttributes;
                if (typeof attributes['class'] !== 'undefined') {
                    classes += attributes['class'];
                }

                return classes;
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
