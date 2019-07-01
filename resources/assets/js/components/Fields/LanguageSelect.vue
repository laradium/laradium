<template>
    <div v-if="data.is_translatable && data.languages.length" class="form-group">
        <label>Language</label>
        <select class="form-control language-select" v-model="data.default_language">
            <option :value="language.iso_code" v-for="language in data.languages">
                {{ language.iso_code }}
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        props: ['data']
    }
</script>