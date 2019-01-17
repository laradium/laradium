<template>
    <a :href="field.value" v-bind="fieldAttributes">
        {{ field.name }}
    </a>
</template>

<script>
    export default {
        props: ['field', 'language', 'item'],
    }
</script>