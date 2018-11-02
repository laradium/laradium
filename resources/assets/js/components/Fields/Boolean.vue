<template>
    <div class="form-group">
        <br>
        <div class="checkbox checkbox-primary">
            <input type="hidden" value="0" :name="field.name">
            <input type="checkbox" value="1" :checked="field.checked" :name="field.name" :id="field.name">
            <label :for="field.name">
                {{ field.label }}
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language', 'item'],
    }
</script>
