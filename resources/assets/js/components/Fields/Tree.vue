<template>
    <div class="row">
        <div>
        </div>
        <div class="col-md-3">
            <div class="pull-right">
                <div class="col-md-12">
                    <button
                            class="btn btn-primary btn-sm"
                            type="button"
                            @click.prevent="addItem()"
                            v-if="field.config.actions.includes('create')">

                        <i class="fa fa-plus"></i> Add item
                    </button>
                </div>
            </div>
            <js-tree :tree="tree" :field="field"></js-tree>
        </div>
        <div class="col-md-9">
            <input type="hidden" :name="field.name + '[crud_worker]'" :value="field.value">
            <div v-for="(entry, index) in field.entries" v-show="!entry.config.is_collapsed">
                <div class="col-md-12 border" style="border-radius: 2px; margin: 5px 5px 5px 0;">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="d-inline-block" v-if="entry.config.is_deleted">
                                <span v-html="entry.label"></span> <span><i>Deleted</i></span>
                            </h4>

                            <div class="pull-right" style="margin-top: 7px;">
                                <button class="btn btn-success btn-sm" v-if="!entry.config.is_deleted"
                                        @click.prevent="toggle(index)">
                                    <span v-if="!entry.config.is_collapsed"><i class="fa fa-eye-slash"></i></span>
                                    <span v-else><i class="fa fa-eye"></i></span>
                                </button>
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
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row" v-show="!entry.config.is_collapsed">
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
            </div>
        </div>
    </div>
</template>

<script>
    import {serverBus} from '../../laradium';

    export default {
        props: ['field', 'language', 'replacement_ids'],
        created: function () {
            this.tree = this.field.tree;
        },
        data() {
            return {
                tree: [],
                new_replacement_ids: {},
                removed_items: {}
            };
        },
        methods: {
            toggle(index) {
                let config = this.field.entries[index].config;
                if (config.is_collapsed !== undefined) {
                    this.field.entries[index].config.is_collapsed = !config.is_collapsed;
                }
            },
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
                    fields: template_fields,
                    id: generate_replacements.id,
                    config: {
                        is_deleted: false,
                        is_collapsed: false
                    },
                    label: 'Entry'
                });

                serverBus.$emit('added_tree_item', generate_replacements.id);
            },

            remove(item, field_name, index) {
                let $vm = this;

                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this after you press 'Save'!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                })
                    .then(function (result) {
                        if (result.value) {
                            if (item.id !== undefined) {
                                console.log(2);
                                let item_copy = _.cloneDeep($vm.field.entries[index]);
                                $vm.removed_items[index] = item_copy;

                                $vm.field.entries[index].fields = [{
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
                                if ($vm.field.entries[index].config.is_deleted !== undefined) {
                                    $vm.field.entries[index].config.is_deleted = true;
                                }
                            } else {
                                $vm.field.entries.splice(index, 1);
                            }
                        }
                    });

            },

            restore(index) {
                this.field.entries[index].fields = this.removed_items[index].fields;
                if (this.field.entries[index].config.is_deleted !== undefined) {
                    this.field.entries[index].config.is_deleted = false;
                }
            }
        }
    }
</script>