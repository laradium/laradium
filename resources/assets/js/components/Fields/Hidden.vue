<template>
    <input type="hidden" :value="input.value" :name="input.name">
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],
    }
</script>
