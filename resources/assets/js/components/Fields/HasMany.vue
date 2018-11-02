<template>
    <div class="border" style="padding: 10px; border-radius: 2px; margin: 5px;">
        <h4>
            <i class="fa fa-bars"></i> {{ field.label }}
        </h4>
        <input type="hidden" :name="field.name + '[crud_worker]'" :value="field.value">
        <draggable class="dragArea" :list="field.entries" @update="onUpdate(field.entries)" :options="draggable">
            <div v-for="(entry, index) in field.entries">
                <div class="col-md-12 border" style="padding: 10px; border-radius: 2px; margin: 5px;">
                    <h4>
                        <span v-if="entry.config.is_deleted"><i>Deleted</i></span>
                        <i class="mdi mdi-arrow-all handle" v-if="field.config.is_sortable && !entry.config.is_deleted"></i>
                        <div class="pull-right" v-if="!entry.config.is_deleted">
                            <button class="btn btn-danger btn-sm"
                                    @click.prevent="remove(entry, field.name, index)"
                                    v-if="field.config.actions.includes('delete')"><i
                                    class="fa fa-trash"></i></button>
                        </div>
                    </h4>
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
        </draggable>
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
                new_replacement_ids: {}
            };
        },

        mounted() {
            this.draggable.disabled = !this.field.config.is_sortable;
        },

        methods: {
            addItem() {
                this.new_replacement_ids = this.generateReplacementIds(this.replacement_ids, this.field.template_data.replacement_ids);
                let template_fields = JSON.parse(JSON.stringify(this.field.template_data.fields));

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
                    fields: template_fields,
                    config: {
                        is_deleted: false
                    }
                });

            },

            onUpdate(items) {
                let i = 0;
                for (let item in items) {
                    let fields = items[item].fields;
                    for (let field in fields) {
                        if (fields[field].label == 'Sequence no') {
                            fields[field].value = i;
                        }
                    }
                    i++;
                }
            },

            remove(item, field_name, index) {
                swal({
                    title: "Are you sure?",
                    text: "After clicking \"Save\", you will not be able to recover this item!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            if (item.id !== undefined) {
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

            toggle() {
                this.field.show = !this.field.show;
            }
        }
    }
</script>
