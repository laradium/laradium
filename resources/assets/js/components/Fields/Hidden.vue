<template>
    <input type="hidden" :value="input.value" :name="input.name" v-bind="attributes">
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],
    }
</script>
