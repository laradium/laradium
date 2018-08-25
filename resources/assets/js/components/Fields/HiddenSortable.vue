<template>
    <div>
        <input type="hidden" :value="item.order" :name="input.name" >
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],
    }
</script>
