<template>
    <div class="row" v-bind="fieldAttributes">
        <template v-if="data.config.use_block">
            <block-field v-for="(block, index) in blocks" :data="block" :language="language"
                         :key="'block' + index">
            </block-field>
        </template>
        <template v-else>
            <col-field v-for="(block, index) in blocks" :data="block" :language="language"
                       :key="'col' + index">
            </col-field>
        </template>
    </div>
</template>

<script>
    export default {
        props: ['data', 'language'],

        data() {
            return {
                current_tab: '',
                blocks: [],
            };
        },

        created() {
            let fields = this.data.fields;
            for (let field in fields) {
                if (fields.hasOwnProperty(field)) {
                    if (this.data.config.use_block) {
                        if (fields[field].type === 'block') {
                            this.blocks.push(fields[field]);
                        }
                    } else {
                        if (fields[field].type === 'col') {
                            this.blocks.push(fields[field]);
                        }
                    }
                }
            }

            if (!this.blocks.length) {
                this.blocks.push({
                    fields: this.data.fields,
                    config: {
                        col: 'col-12'
                    }
                });
            }
        },
    }
</script>