<template>
    <div class="border">
        <h4>
            <i class="fa fa-bars"></i> {{ field.label }}
            <span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
        </h4>

        <input type="hidden" :name="field.name + '[crud_worker]'" :value="field.value">

        <table class="table table-striped">
            <thead>
            <tr>
                <th v-for="templateField in field.template_data.fields">
                    {{ templateField.label }}
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(entry, index) in field.entries">
                <template v-if="entry.config.is_deleted">
                    <td v-for="(templateField, index) in field.template_data.fields" class="deleted">
                        <span v-if="index === 0">
                            <em>Deleted</em>
                        </span>
                    </td>
                </template>

                <td v-for="(field, index) in entry.fields" :class="{'hidden': field.type === 'hidden'}">
                    <component
                        :is="field.type + '-field'"
                        :field="field"
                        :language="language"
                        :replacement_ids="new_replacement_ids"
                        :key="index"
                    ></component>
                </td>

                <td class="actions">
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
                </td>
            </tr>
            </tbody>
        </table>

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
                new_replacement_ids: {},
                removed_items: {}
            };
        },

        methods: {
            addItem() {
                let generate_replacements = this.generateReplacementIds(this.replacement_ids, this.field.template_data.replacement_ids);
                this.new_replacement_ids = generate_replacements.replacement_ids;
                let template_fields = _.cloneDeep(this.field.template_data.fields);

                for (let field in template_fields) {
                    if (!template_fields.hasOwnProperty(field)) {
                        continue;
                    }

                    for (let id in this.new_replacement_ids) {
                        if (!this.new_replacement_ids.hasOwnProperty(id)) {
                            continue;
                        }

                        if (!template_fields[field].config.is_translatable) {
                            template_fields[field].name = template_fields[field].name.replace(id, this.new_replacement_ids[id]);
                        } else {
                            let translations = template_fields[field].translations;

                            for (let translation in translations) {
                                if (!translations.hasOwnProperty(translation)) {
                                    continue;
                                }

                                translations[translation].name = translations[translation].name.replace(id, this.new_replacement_ids[id]);
                            }
                        }
                    }
                }

                this.field.entries.push({
                    fields: template_fields,
                    config: {
                        is_deleted: false,
                        is_collapsed: false
                    },
                    label: 'Entry'
                });

            },

            async remove(item, field_name, index) {
                let result = await swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this after you press 'Save'!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                });

                if (result.value) {
                    if (typeof item.id === 'undefined') {
                        this.field.entries.splice(index, 1);

                        return true;
                    }

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

                    if (this.field.entries[index].config.is_deleted !== undefined) {
                        this.field.entries[index].config.is_deleted = true;
                    }

                    return true;
                }
            },

            restore(index) {
                this.field.entries[index].fields = this.removed_items[index].fields;
                if (this.field.entries[index].config.is_deleted !== undefined) {
                    this.field.entries[index].config.is_deleted = false;
                }
            },
        }
    }
</script>

<style lang="scss">
    td {
        &.actions {
            width: 110px;
        }

        &.deleted {
            vertical-align: middle;
        }

        label {
            display: none;
        }

        .form-group {
            margin: 0;
        }
    }

    .border {
        padding: 0 10px 10px 10px;
        border-radius: 2px;
        margin: 0 0 10px;
    }
</style>
