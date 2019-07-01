<template>
    <div class="row">
        <template v-for="field in data.fields">
            <component :is="field.type + '-field'"
                       :field="field"
                       :data="field"
                       :current_tab="current_tab"
                       :language="language"
                       :replacement_ids="{}"
            ></component>
        </template>
    </div>
</template>

<script>
    export default {
        name: 'InterfaceBuilder',

        props: ['builder_data'],

        data() {
            return {
                language: '',
                current_tab: '',
                success: '',
                is_translatable: false,
                errors: [],
                data: [],
                rows: [],
                loading: true,
                isSubmitted: false
            };
        },

        created() {
            this.data = JSON.parse(this.builder_data).data;

            let fields = this.data.fields;

            let i = 0;
            for (let field in fields) {
                if (fields.hasOwnProperty(field)) {
                    if (fields[field].type === 'tab') {
                        if (i === 0) {
                            this.current_tab = fields[field].slug;
                        }
                        this.tabs.push({
                            slug: fields[field].slug,
                            name: fields[field].name,
                        });
                    }
                    i++;
                }
            }

            if (!this.rows.length) {
                this.rows.push({
                    fields: this.data.fields,
                    config: {
                        use_block: true,
                        col: 'col-md-12'
                    }
                });
            }
        },

        methods: {
            countFieldsByType(type, fields) {
                if (!this.data) {
                    return 0;
                }

                if (typeof fields === 'undefined') {
                    fields = this.data.fields;
                }

                let count = 0;

                fields.forEach(field => {
                    if (field.type === type) {
                        count++;
                    }

                    if (field.fields && field.fields.length) {
                        count += this.countFieldsByType(type, field.fields)
                    }
                });

                return count;
            }
        }
    }
</script>
