<template>
    <div v-bind="fieldAttributes" v-html="field.content"></div>
</template>

<script>
    export default {
        props: ['field', 'language', 'item'],
    }
</script>
