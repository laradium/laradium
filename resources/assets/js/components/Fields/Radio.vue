<template>
    <div>
        <div class="form-group">
            <label :for="field.name">{{ field.label }}</label>

            <div v-for="option in field.options">
                <input type="radio" :name="field.name" :value="option.value" :checked="option.checked"> {{ option.text }} <br/>
            </div>
        </div>
    </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language', 'item'],

        computed: {
            attributes() {
                return this.field.attr;
            }
        }
    }
</script>
