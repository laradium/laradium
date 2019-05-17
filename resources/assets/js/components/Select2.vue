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
                    height: '38px',
                    escapeMarkup: (markup) => {
                        return markup;
                    },
                    allowClear: true
                }
            };
        },
        mounted: function () {
            let config = this.config ? this.config : this.defaultConfig;
            let select = $(this.$el);

            select
                .val(this.value)
                .select2(config)
                .on('change', () => {
                    this.$emit('input', select.val())
                });
        },
        watch: {
            value: function (value) {
                $(this.$el).val(value)
            },
            options: function (options) {
                $(this.$el).select2({ data: options })
            }
        },
        destroyed: () => {
            $(this.$el).off().select2('destroy');
        }
    }
</script>
