<template>
    <div class="form-group">
        <div class="checkbox checkbox-primary">
            <input type="hidden" value="0" :name="input.name">
            <input type="checkbox" value="1" :checked="input.checked" :name="input.name" :id="input.name">
            <label :for="input.name">
                {{ input.label }}
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],
    }
</script>
