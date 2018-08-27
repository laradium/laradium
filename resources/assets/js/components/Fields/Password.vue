<template>
    <div class="form-group">
        <label for="">{{ input.label }}</label>

        <input type="password" :name="input.name" class="form-control">
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item']
    }
</script>
