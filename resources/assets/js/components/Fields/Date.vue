<template>
    <div class="form-group">
        <label for="">{{ input.label }}
            <span class="badge badge-primary"
                  v-if="input.isTranslatable">
                {{ language }}
            </span>
        </label>
        <div v-if="input.isTranslatable">
            <datepicker :name="input.name"
                        v-model="item.value"
                        :key="index"
                        v-for="(item, index) in input.translatedAttributes"
                        v-show="language === item.iso_code"
                        class="form-control"
                        placeholder="Select date">
            </datepicker>
        </div>
        <div v-else>
            <datepicker :name="input.name"
                        :value="input.value"
                        class="form-control"
                        placeholder="Select date">
            </datepicker>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['input', 'language', 'item']
    }

    Vue.component('datepicker', {
        props: ['value'],
        template: '<input type="text" \
            v-bind:value="value" readonly \/>',

        mounted: function () {
            $(this.$el).datetimepicker({
                timepicker:false,
                format: 'Y-m-d',
                dayOfWeekStart: 1
            });
        }
    });
</script>
