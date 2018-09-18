<template>
    <div class="form-group">
        <label for="">{{ input.label }}
            <span class="badge badge-primary"
                  v-if="input.isTranslatable">
                {{ language }}
            </span>
        </label>
        <br>
        <div v-if="input.isTranslatable">
            <a v-for="item in input.translatedAttributes" :href="item.url" v-if="item.url" v-show="language === item.iso_code" target="_blank">
                {{ item.file_name }} ({{ item.file_size }} kb)
            </a>
            <input type="file" :name="item.name" class="form-control" v-for="item in input.translatedAttributes"
                   v-show="language === item.iso_code" v-bind="attributes">
        </div>
        <div v-else>
            <a :href="input.url" v-if="input.url" target="_blank">
                {{ input.file_name }} ({{ input.file_size }} kb)
            </a>
            <input type="file" :name="input.name" class="form-control" v-bind="attributes">
        </div>
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
