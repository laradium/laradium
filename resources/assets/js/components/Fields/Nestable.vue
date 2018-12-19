<template>
    <draggable :element="'ul'"
               :list="entries"
               class="dragArea"
               :options="draggable"
               :move="cancel"
               @change="change"
               @start="start"
               @update="onUpdate(entries)">
        <li v-for="(entry, index) in entries" style="list-style-type: none;">
            <h4>
                <i class="mdi mdi-arrow-all handle" v-if="field.config.is_sortable && !entry.config.is_deleted"></i>

                <span v-html="entry.label"></span> <span v-if="entry.config.is_deleted"><i>Deleted</i></span>

                <div class="pull-right" v-if="entry.config.is_deleted">
                    <button class="btn btn-primary btn-sm"
                            @click.prevent="restore(entry)"
                            v-if="field.config.actions.includes('delete')">
                        <i class="fa fa-undo"></i> Restore
                    </button>
                </div>

                <div class="pull-right" v-if="!entry.config.is_deleted">
                    <button class="btn btn-danger btn-sm"
                            @click.prevent="remove(entry, field.name)"
                            v-if="field.config.actions.includes('delete')"><i
                            class="fa fa-trash"></i></button>
                </div>

                <div class="pull-right" v-if="!entry.config.is_deleted">
                    <button class="btn btn-success btn-sm" @click.prevent="toggle(entry)" style="margin-right: 5px;">
                        <span v-if="!entry.config.is_collapsed"><i class="fa fa-eye-slash"></i></span>
                        <span v-else><i class="fa fa-eye"></i></span>
                    </button>
                </div>
            </h4>
            <div class="row border" v-show="!entry.config.is_collapsed"
                 style="padding: 5px; border-radius: 2px; margin: 5px;">
                <div v-for="(field, index) in entry.fields" :class="field.config.col">
                    <component
                            :is="field.type + '-field'"
                            :field="field"
                            :language="language"
                            :replacement_ids="replacement_ids"
                            :key="index"
                    ></component>
                </div>
            </div>
            <nestable v-if="entry.children"
                      :field="field"
                      :entries="entry.children"
                      :language="language"
                      :replacement_ids="replacement_ids"
                      class="col-md-12 border"
                      style="padding: 10px; border-radius: 2px; margin: 5px;">
            </nestable>

        </li>
    </draggable>
</template>
<script>
    import {serverBus} from '../../laradium';

    export default {
        props: ['field', 'entries', 'language', 'replacement_ids'],

        created() {
            serverBus.$emit('form_data', this.field.entries);
        },

        data() {
            return {
                removed_items: {},
                draggable: {
                    handle: '.handle',
                    group: {
                        name: 'g1'
                    }
                },
            };
        },

        methods: {
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
                    .then(function (result) {
                        if (result.value) {
                            if (item.id !== undefined && Number.isInteger(item.id)) {
                                let item_copy = _.cloneDeep(item);
                                this.removed_items[item.id] = item_copy;

                                item.fields = [{
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
                                item.config.is_deleted = true;
                            } else {
                                this.removeEntry(this.field.entries, item.id);
                            }
                        }
                    });

            },

            removeEntry(entries, id) {
                for (let entry in entries) {
                    if (entries[entry].id == id) {
                        entries.splice(entry, 1);
                        break;
                    }

                    if (entries[entry].children.length) {
                        this.removeEntry(entries[entry].children, id);
                    }
                }
            },

            restore(entry) {
                entry.fields = this.removed_items[entry.id].fields;
                if (entry.config.is_deleted !== undefined) {
                    entry.config.is_deleted = false;
                }
            },

            toggle(entry) {
                entry.config.is_collapsed = !entry.config.is_collapsed;
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

            cancel() {
                return this.canSort();
            },

            canSort() {
                let can = true;
                for (let item in this.field.entries) {
                    if (!Number.isInteger(this.field.entries[item].id)) {

                        can = false;
                    }
                }

                return can;
            },

            change(evt) {
                let element = {};
                serverBus.$emit('form_data', this.field.entries);

                for (let item in evt) {
                    element = evt[item].element;
                }
                this.getParent(this.field.entries, element.id);
            },

            start() {
                if (!this.canSort()) {
                    toastr.warning('You need to save new data before sorting!');
                }
            },

            getParent(entries, id, parent = null) {
                for (let item in entries) {
                    if (entries[item].id == id) {
                        for (let field in entries[item].fields) {
                            if (entries[item].fields[field].label == 'Parent id') {
                                entries[item].fields[field].value = parent;
                            }
                        }
                    }

                    if (entries[item].children.length) {
                        this.getParent(entries[item].children, id, entries[item].id);
                    }
                }

                return 0;
            }
        }
    }
</script>
