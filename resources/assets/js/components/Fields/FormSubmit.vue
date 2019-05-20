<template>
    <div class="form-group">
        <button :data-form_button="field.form_name" v-html="field.label" v-bind="fieldAttributes" @click.prevent="triggerSubmit">

        </button>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language', 'item'],
        methods: {
            triggerSubmit() {
                let formName = this.field.form_name;
                $('form[data-name="' + formName + '"]').trigger('submit-crud');
            }
        }

    }
</script>