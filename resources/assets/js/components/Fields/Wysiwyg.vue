<template>
    <div class="form-group">
        <label for="">{{ input.label }}
            <span class="badge badge-primary"
                  v-if="input.isTranslatable">
                {{ language }}
            </span>
        </label>
        <div v-if="input.isTranslatable">
                <input type="hidden" v-model="item.value"
                       :name="item.name"
                       v-for="item in input.translatedAttributes">

                <wysiwyg
                        v-model="item.value"
                        :name="item.name"
                        v-for="item in input.translatedAttributes"
                        :key="item.name"
                        v-show="language === item.iso_code"></wysiwyg>

        </div>
        <div v-else>
            <textarea :name="input.name" class="form-control">{{ input.value }}</textarea>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item'],
        mounted() {

        }
    }
</script>
