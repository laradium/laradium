<template>
    <div :class="data.config.col">
        <div class="card-box table-responsive">
            <slot></slot>

            <div class="row">
                <col-field v-for="(column, index) in columns" :data="column" :language="language"
                           :key="'col' + index"></col-field>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['data', 'language'],

        data() {
            return {
                columns: []
            };
        },

        created() {
            let fields = this.data.fields;
            for (let field in fields) {
                if (fields.hasOwnProperty(field)) {
                    if (fields[field].type === 'col') {
                        this.columns.push(fields[field]);
                    }
                }
            }

            if (!this.columns.length) {
                this.columns.push({
                    name: 'col-md-12',
                    slug: 'col-md-12',
                    fields: this.data.fields,
                    config: {
                        col: 'col-md-12'
                    }
                });
            }
        },

        computed: {
            attributes() {
                return this.field.attr;
            }
        }
    }
</script>
