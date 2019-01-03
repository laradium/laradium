<template>
    <div class="form-group">
        <label for="">
            {{ field.label }}
            <span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
            <span class="badge badge-primary"
                  v-if="field.config.is_translatable">
                {{ language }}
            </span>
        </label>
        <div v-if="field.config.is_translatable">
            <timepicker :name="field.name"
                        v-model="item.value"
                        :key="index"
                        v-for="(item, index) in field.translations"
                        v-show="language === item.iso_code"
                        class="form-control"
                        placeholder="Select date">
            </timepicker>
        </div>
        <div v-else>
            <timepicker :name="field.name"
                        v-model="field.value"
                        class="form-control"
                        placeholder="Select time">
            </timepicker>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language', 'item']
    }

    Vue.component('timepicker', {
        props: ['value'],
        template: '<input type="text" \
            v-bind:value="value" readonly \/>',

        mounted: function () {
            $(this.$el).datetimepicker({
                format: 'H:i',
                datepicker: false,
            });
        }
    });
</script>
