<template>
    <div v-if="field.config.with_form_group" class="form-group">
        <button v-bind="fieldAttributes">
            {{ field.name }}
        </button>
    </div>
    <button v-else v-bind="fieldAttributes">
        {{ field.name }}
    </button>
</template>

<script>
    export default {
        props: ['field', 'language', 'item'],
    }
</script>