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
            <datetimepicker :name="field.name"
                            v-model="item.value"
                            :key="index"
                            v-for="(item, index) in field.translations"
                            v-show="language === item.iso_code"
                            class="form-control"
                            placeholder="Select date/time">
            </datetimepicker>
        </div>
        <div v-else>
            <datetimepicker :name="field.name"
                            v-model="field.value"
                            class="form-control"
                            placeholder="Select date/time">
            </datetimepicker>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language']
    }

    Vue.component('datetimepicker', {
        props: ['value'],
        template: '<input type="text" \
            v-bind:value="value" readonly \/>',

        mounted: function () {
            $(this.$el).datetimepicker({
                format: 'Y-m-d H:i',
                dayOfWeekStart: 1
            });
        }
    });
</script>
