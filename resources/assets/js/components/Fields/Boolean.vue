<template>
    <div class="form-group">
        <label for="">
            {{ input.label }}
        </label>
        <input type="hidden" value="0" :name="input.name">
        <input type="checkbox" value="1" :checked="input.checked" :name="input.name" class="form-check-inline">
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],
    }
</script>
