<template>
    <div class="border" style="padding: 5px; border-radius: 2px; margin: 2px;">
        <h4>
            <i class="fa fa-bars"></i> {{ field.label }}
            <span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
        </h4>
        <input type="hidden" :name="field.name + '[crud_worker]'" :value="field.value">

        <nestable
                :language="language"
                :replacement_ids="new_replacement_ids"
                :field="field"
                :entries="field.entries">
        </nestable>

        <div class="row">
            <div class="col-md-12">
                <button
                        class="btn btn-primary btn-sm"
                        type="button"
                        @click.prevent="addItem()"
                        v-if="field.config.actions.includes('create')">

                    <i class="fa fa-plus"></i> Add {{ field.label }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language', 'replacement_ids'],

        data() {
            return {
                draggable: {
                    disabled: true,
                    handle: '.handle'
                },
                new_replacement_ids: {},
                removed_items: {},
                fieldCopy: {}
            };
        },

        mounted() {
            this.draggable.disabled = !this.field.config.is_sortable;
        },

        methods: {
            addItem() {
                let generate_replacements = this.generateReplacementIds(this.replacement_ids, this.field.template_data.replacement_ids);
                this.new_replacement_ids = generate_replacements.replacement_ids;
                let template_fields = _.cloneDeep(this.field.template_data.fields);

                for (let field in template_fields) {
                    for (let id in this.new_replacement_ids) {
                        if (!template_fields[field].config.is_translatable) {
                            template_fields[field].name = template_fields[field].name.replace(id, this.new_replacement_ids[id]);
                        } else {
                            let translations = template_fields[field].translations;
                            for (let translation in translations) {
                                translations[translation].name = translations[translation].name.replace(id, this.new_replacement_ids[id]);
                            }
                        }
                    }
                }

                this.field.entries.push({
                    id: generate_replacements.id,
                    fields: template_fields,
                    config: {
                        is_deleted: false,
                        is_collapsed: false
                    },
                    children: [],
                    label: 'Entry'
                });

            },
        }
    }
</script>