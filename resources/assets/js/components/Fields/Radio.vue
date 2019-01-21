<template>
    <div>
        <div class="form-group">
            <label :for="field.name">
                {{ field.label }}
                <span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
            </label>

            <div v-for="option in field.options">
                <input type="radio" :name="field.name" :value="option.value" :checked="option.checked"> {{ option.text
                }} <br/>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language'],
    }
</script>
