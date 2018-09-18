<template>
    <div class="form-group">
        <label for="">{{ input.label }}
            <span class="badge badge-primary"
                  v-if="input.isTranslatable">
                {{ language }}
            </span>
        </label>
        <div v-if="input.isTranslatable">
            <input type="text"
                   v-model="item.value"
                   :name="item.name"
                   v-for="item in input.translatedAttributes"
                   v-show="language === item.iso_code"
                   class="form-control">
        </div>
        <div v-else>
            <input type="text" :value="input.value" :name="input.name" class="form-control" v-bind="attributes">
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
