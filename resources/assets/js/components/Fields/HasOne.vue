<template>
    <div class="border" style="padding: 10px; border-radius: 2px; margin: 5px;">
        <h4>
            <i class="fa fa-bars"></i> {{ field.label }}
            <span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
        </h4>
        <input type="hidden" :name="field.name + '[crud_worker]'" :value="field.value">
        <div v-for="(entry, index) in field.entries">
            <div class="col-md-12 border" style="padding: 10px; border-radius: 2px; margin: 5px;">
                <h4 class="d-inline-block">
                    <span>Entry</span> <span v-if="entry.config.is_deleted"><i>Deleted</i></span>
                </h4>

                <div class="pull-right" style="margin-top: 7px;">
                    <button class="btn btn-primary btn-sm"
                            @click.prevent="restore(index)"
                            v-if="entry.config.is_deleted && field.config.actions.includes('delete')">
                        <i class="fa fa-undo"></i> Restore
                    </button>

                    <button class="btn btn-danger btn-sm"
                            @click.prevent="remove(entry, field.name, index)"
                            v-if="!entry.config.is_deleted && field.config.actions.includes('delete')">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div v-for="(field, index) in entry.fields" :class="field.config.col">
                        <component
                                :is="field.type + '-field'"
                                :field="field"
                                :language="language"
                                :replacement_ids="new_replacement_ids"
                                :key="index"
                        ></component>
                    </div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button
                        class="btn btn-primary btn-sm"
                        type="button"
                        @click.prevent="addItem()"
                        v-if="field.entries.length === 0"
                >

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
                new_replacement_ids: {},
                removed_items: {}
            };
        },

        methods: {
            addItem() {
                let generate_replacements = this.generateReplacementIds(this.replacement_ids, this.field.template_data.replacement_ids);
                this.new_replacement_ids = generate_replacements.replacement_ids;
                let template_fields = JSON.parse(JSON.stringify(this.field.template_data.fields));

                for (let field in template_fields) {
                    for (let id in this.new_replacement_ids) {
                        if (template_fields[field].worker) {
                            template_fields[field].worker.name = template_fields[field].worker.name.replace(id, this.new_replacement_ids[id]);
                        }

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
                    fields: template_fields,
                    config: {
                        is_deleted: false
                    }
                });
            },

            remove(item, field_name, index) {
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this after you press 'Save'!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                })
                    .then((result) => {
                        if (result.value) {
                            if (item.id !== undefined) {
                                this.removed_items[index] = _.cloneDeep(this.field.entries[index]);

                                this.field.entries[index].fields = [{
                                    type: "hidden",
                                    label: "Id",
                                    name: field_name + "[" + item.id + "][remove]",
                                    value: 1,
                                    config: {
                                        is_translatable: false,
                                    },
                                    translations: []
                                }, {
                                    type: "hidden",
                                    label: "Id",
                                    name: field_name + "[" + item.id + "][id]",
                                    value: item.id,
                                    config: {
                                        is_translatable: false,
                                    },
                                    translations: []
                                }];
                                this.field.entries[index].config = {
                                    is_deleted: true
                                };
                            } else {
                                this.field.entries.splice(index, 1);
                            }
                        }
                    });
            },

            restore(index) {
                this.field.entries[index].fields = this.removed_items[index].fields;
                this.field.entries[index].config = {
                    is_deleted: false
                };
            }
        }
    }
</script>
