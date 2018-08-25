<template>
    <div>
        <div class="form-group" v-if="!input.isHidden">
            <label for="">
                {{ input.label }}
            </label>
            <select :name="input.name" id="" class="form-control">
                <option
                        :value="option.value"
                        :selected="option.selected"
                        v-for="option in input.options">
                    {{ option.text }}
                </option>
            </select>
        </div>
        <div v-if="input.isHidden">
            <input type="hidden" :name="input.name" :value="input.default">
        </div>
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],
        mounted() {
            console.log('Mounted');
        },
        created() {
            console.log('Created');
        }
    }
</script>
