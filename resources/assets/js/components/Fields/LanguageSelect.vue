<template>
    <div v-if="data.is_translatable && data.languages.length" class="form-group">
        <label>Language</label>
        <select class="form-control language-select" v-model="currentLanguage" @change="changeLanguage">
            <option :value="language.iso_code" v-for="language in data.languages">
                {{ language.iso_code }}
            </option>
        </select>
    </div>
</template>

<script>
    import {serverBus} from '../../laradium';

    export default {
        props: ['data'],

        data() {
            return {
                currentLanguage: this.data.languages[0].iso_code
            }
        },

        methods: {
            changeLanguage() {
                serverBus.$emit('change_language', this.currentLanguage);
            }
        }
    }
</script>
