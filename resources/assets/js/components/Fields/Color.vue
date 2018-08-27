<template>
    <div class="form-group">
        <label for="">{{ input.label }}
            <span class="badge badge-primary"
                  v-if="input.isTranslatable">
                {{ language }}
            </span>
        </label>
        <div v-if="input.isTranslatable">
            <colorpicker :name="input.name"
                         v-model="item.value"
                         :key="index"
                         v-for="(item, index) in input.translatedAttributes"
                         v-show="language === item.iso_code"
                         class="form-control"
                         placeholder="Select color">
            </colorpicker>
        </div>
        <div v-else>
            <colorpicker :name="input.name"
                        :value="input.value"
                        class="form-control"
                        placeholder="Select color">
            </colorpicker>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item']
    }

    Vue.component('colorpicker', {
        props: ['value'],
        template: '<input type="text" \
            v-bind:value="value" readonly \/>',

        mounted: function () {
            $(this.$el).colorpicker({
                format: 'hex'
            });
        }
    });
</script>
