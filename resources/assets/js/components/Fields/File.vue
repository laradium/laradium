<template>
    <div class="form-group">
        <label for="">
            {{ input.label }}
        </label>
        <br>
        <a :href="input.url" v-if="input.url" target="_blank">
            {{ input.file_name }} ({{ input.file_size }} kb)
        </a>
        <input type="file" :name="input.name" class="form-control">
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],
    }
</script>
