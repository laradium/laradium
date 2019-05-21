<template>
    <select class="form-control" v-bind:multiple="multiple">
        <slot></slot>
    </select>
</template>

<script>
    import _ from 'lodash';
    import axios from 'axios';

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

        async mounted() {
            Object.assign(this.defaultConfig, this.config);

            let config = this.config ? this.config : this.defaultConfig;
            let select = $(this.$el);

            if (this.config.source) {
                let selected = await this.findSelected();

                if (selected) {
                    this.appendOption(selected);
                }

                config.ajax = {
                    url: this.config.source,
                    data: (params) => this.processQuery(params),
                    processResults: (data) => this.processResults(data)
                }
            }

            select
                .val(this.value)
                .select2(config)
                .on('change', () => {
                    this.$emit('input', select.val())
                });
        },

        methods: {
            async findSelected() {
                let response = await axios.get(this.config.source, this.config.query_params);

                let data = response.data;
                if (this.config.data_property) {
                    data = response.data[this.config.data_property];
                }

                return _.find(data, item => {
                    return item[this.config.id_field] == this.value;
                });
            },

            appendOption(item) {
                $(this.$el).append(`<option value="${item[this.config.id_field]}" selected="true">${item[this.config.text_field]}</option>`)
            },

            processQuery(params) {
                let query = {};

                query[this.config.search_param] = params.term;

                for (let key in this.config.query_params) {
                    if (!this.config.query_params.hasOwnProperty(key)) {
                        continue
                    }

                    query[key] = this.config.query_params[key];
                }

                return query;
            },

            processResults(data) {
                let response = _.cloneDeep(data);

                if (this.config.data_property) {
                    response = response[this.config.data_property];
                }

                let results = _.map(response, item => {
                    let disabled = typeof item.disabled !== 'undefined' ? item.disabled : false;
                    let selected = typeof item.selected !== 'undefined' ? item.selected : false;

                    return {
                        id: item[this.config.id_field],
                        text: item[this.config.text_field],
                        selected: selected,
                        disabled: disabled,
                    }
                });

                return {
                    results: results
                }
            }
        },

        watch: {
            value: function (value) {
                $(this.$el).val(value)
            },
            options: function (options) {
                $(this.$el).select2({
                    data: options
                })
            }
        },

        destroyed() {
            $(this.$el).off().select2('destroy');
        }
    }
</script>
