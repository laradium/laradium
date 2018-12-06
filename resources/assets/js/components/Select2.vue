<template>
    <select>
        <slot></slot>
    </select>
</template>

<script>
    export default {
        props: ['options', 'value'],
        mounted: function () {
            let vm = this;
            $(this.$el)
                .select2({
                    data: this.options,
                    placeholder: 'Select',
                    width: '100%',
                    height: '100px',
                    // templateResult: function (d) { return $(d.text); },
                    // templateSelection: function (d) { return $(d.text); },
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                    // templateSelection: function(data) {
                    //     return data.text;
                    // }
                })
                .val(this.value)
                .trigger('change')
                // emit event on change.
                .on('change', function () {
                    vm.$emit('input', this.value)
                });
        },
        watch: {
            value: function (value) {
                $(this.$el)
                    .val(value)
                    .trigger('change');
            },
            options: function (options) {
                $(this.$el).empty().select2({data: options});
            }
        },
        destroyed: function () {
            $(this.$el).off().select2('destroy');
        }
    }
</script>
