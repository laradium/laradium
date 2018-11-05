<template>
    <div class="form-group">
        <label for="">{{ field.label }}
            <span class="badge badge-primary"
                  v-if="field.config.is_translatable">
                {{ language }}
            </span>
        </label>
        <div v-if="field.config.is_translatable">
            <datepicker :name="field.name"
                        v-model="item.value"
                        :key="index"
                        v-for="(item, index) in field.translations"
                        v-show="language === item.iso_code"
                        class="form-control"
                        placeholder="Select date">
            </datepicker>
        </div>
        <div v-else>
            <datepicker :name="field.name"
                        v-model="field.value"
                        class="form-control"
                        placeholder="Select date">
            </datepicker>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language', 'item']
    }

    Vue.component('datepicker', {
        props: ['value'],
        template: '<input type="text" \
            v-bind:value="value" readonly \/>',

        mounted: function () {
            $(this.$el).datetimepicker({
                timepicker: false,
                format: 'Y-m-d',
                dayOfWeekStart: 1
            });
        }
    });
</script>
