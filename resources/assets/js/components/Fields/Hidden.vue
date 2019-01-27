<template>
    <input type="hidden" :value="field.value" :name="field.name" v-bind="attributes">
</template>

<script>
    export default {
        props: ['field', 'language', 'attributes'],
    }
</script>
