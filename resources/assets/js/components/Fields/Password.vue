<template>
    <div class="form-group">
        <label for="">{{ input.label }}</label>

        <input type="password" :name="input.name" class="form-control" v-bind="attributes">
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],

        computed: {
            attributes() {
                return this.input.attr;
            }
        }
    }
</script>
