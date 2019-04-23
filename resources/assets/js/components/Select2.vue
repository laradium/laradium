<template>
    <select class="form-control" v-bind:multiple="multiple">
        <slot></slot>
    </select>
</template>

<script>
    export default {
        props: ['options', 'value', 'config', 'multiple'],
        data() {
            return {
                defaultConfig: {
                    data: this.options,
                    placeholder: 'Select',
                    width: '100%',
                    escapeMarkup: (markup) => {
                        return markup;
                    },
                    allowClear: true
                }
            };
        },
        mounted: function () {
            let config = this.config ? this.config : this.defaultConfig;
            let select = $(this.$el)

            select
                .select2(config)
                .on('change', () => {
                    this.$emit('input', select.val())
                })

            select.val(this.value).trigger('change')
        },
        destroyed: () => {
            $(this.$el).off().select2('destroy');
        }
    }
</script>
