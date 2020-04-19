<template>
    <div class="border" style="padding: 0px 10px 10px 10px; border-radius: 2px;">
        <h4>
            <i class="fa fa-bars"></i> Widgets
        </h4>
        <input type="hidden" :name="field.name + '[crud_worker]'" :value="field.value">
        <draggable class="dragArea" :list="field.blocks" @update="onUpdate(field.blocks)" :options="draggable">
            <div v-for="(block, index) in field.blocks">
                <div class="col-md-12 border" style="border-radius: 2px; margin: 5px 5px 5px 0;">

                    <h4 class="d-inline-block">
                        <i class="mdi mdi-arrow-all handle"
                           v-if="field.config.is_sortable && !block.config.is_deleted"></i>
                        {{ getBlockTitle(block) }}
                        <span v-if="block.config.is_deleted"><i>Deleted</i></span>
                    </h4>

                    <div class="pull-right" style="margin-top: 7px;">
                        <button class="btn btn-success btn-sm"
                                @click.prevent="toggle(index)"
                                v-if="!block.config.is_deleted">
                            <span v-if="!block.config.is_collapsed"><i class="fa fa-eye-slash"></i></span>
                            <span v-else><i class="fa fa-eye"></i></span>
                        </button>

                        <button class="btn btn-primary btn-sm" v-if="block.config.is_deleted"
                                @click.prevent="restore(index)">
                            <i class="fa fa-undo"></i> Restore
                        </button>

                        <button class="btn btn-danger btn-sm" v-if="!block.config.is_deleted"
                                @click.prevent="remove(block, field.name, index)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>

                    <div class="row" v-show="!block.config.is_collapsed">
                        <div v-for="(field, index) in block.fields" :class="field.config.col">
                            <component
                                    :is="field.type + '-field'"
                                    :field="field"
                                    :language="language"
                                    :replacement_ids="new_replacement_ids"
                                    :key="index">
                            </component>
                        </div>
                    </div>

                </div>
            </div>
        </draggable>
        <div class="row">
            <div class="col-md-2">
                <select class="form-control" v-model="selectedWidget">
                    <option v-for="(item, index) in field.template_data.blocks" :value="item.label"
                            :selected="index === 0">{{ item.label }}
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-md" type="button" @click.prevent="addItem()">
                    <i class="fa fa-plus"></i> Add
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
                selectedWidget: ''
            };
        },

        mounted() {
            this.draggable.disabled = !this.field.config.is_sortable;

            if (typeof this.field.template_data.blocks[0] !== "undefined") {
                this.selectedWidget = this.field.template_data.blocks[0].label;
            }
        },

        methods: {
            addItem() {
                let templateBlocks = this.field.template_data.blocks;
                let template = '';


                for (let block in templateBlocks) {
                    if (templateBlocks[block].label === this.selectedWidget) {
                        template = templateBlocks[block];
                        template = _.cloneDeep(template);
                        let generate_replacements = this.generateReplacementIds(this.replacement_ids, template.replacement_ids);
                        this.new_replacement_ids = generate_replacements.replacement_ids;

                        for (let field in template.fields) {
                            for (let id in this.new_replacement_ids) {
                                if (template.fields[field].worker) {
                                    template.fields[field].worker.name = template.fields[field].worker.name.replace(id, this.new_replacement_ids[id]);
                                }

                                if (!template.fields[field].config.is_translatable) {
                                    template.fields[field].name = template.fields[field].name.replace(id, this.new_replacement_ids[id]);
                                } else {
                                    let translations = template.fields[field].translations;
                                    for (let translation in translations) {
                                        translations[translation].name = translations[translation].name.replace(id, this.new_replacement_ids[id]);
                                    }
                                }
                            }
                        }
                    }
                }

                if (template.config.is_collapsed !== undefined) {
                    template.config.is_collapsed = false;
                }

                this.field.blocks.push(template);
                this.onUpdate(this.field.blocks);
            },

            onUpdate(items) {
                let i = 0;
                for (let item in items) {
                    let fields = items[item].fields;
                    if (items[item].config.is_deleted !== undefined && items[item].config.is_deleted === true) {
                        continue;
                    }
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
                                let item_copy = _.cloneDeep(this.field.blocks[index]);
                                this.removed_items[index] = item_copy;

                                this.field.blocks[index].fields = [{
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

                                if (this.field.blocks[index].config.is_deleted !== undefined) {
                                    this.field.blocks[index].config.is_deleted = true;
                                    this.onUpdate(this.field.blocks);
                                }
                            } else {
                                this.field.blocks.splice(index, 1);
                            }
                        }
                    });

            },

            getBlockTitle(block) {
                let fields = block.fields;

                let field = _.filter(fields, (field) => {
                    return field.label === 'Widget title';
                });

                if (field[0] !== undefined && field[0].value) {
                    return field[0].value;
                }

                return block.label;
            },

            restore(index) {
                this.field.blocks[index].fields = this.removed_items[index].fields;
                if (this.field.blocks[index].config.is_deleted !== undefined) {
                    this.field.blocks[index].config.is_deleted = false;
                    this.onUpdate(this.field.blocks);
                }
            },

            toggle(index) {
                let config = this.field.blocks[index].config;
                if (config.is_collapsed !== undefined) {
                    this.field.blocks[index].config.is_collapsed = !config.is_collapsed;
                }
            }
        }
    }
</script>
