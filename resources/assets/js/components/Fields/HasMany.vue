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
                        <i class="mdi mdi-arrow-all handle" v-if="field.config.isSortable">HANDLE</i>
                        <div class="pull-right">
                            <button class="btn btn-danger btn-sm"
                                    @click.prevent="remove(entry, field.name, index)"
                                    v-if="field.config.actions.includes('delete')"><i
                                    class="fa fa-trash">Delete</i></button>
                        </div>
                    </h4>
                    <div class="row">
                        <div v-for="(field, index) in entry.fields" :class="field.config.col">
                            <component
                                    :is="field.type + '-field'"
                                    :field="field"
                                    :language="language"
                                    :replacementIds="newReplacementIds"
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
        props: ['field', 'language', 'replacementIds'],

        data() {
            return {
                draggable: {
                    disabled: true,
                    handle: '.handle'
                },
                newReplacementIds: {}
            };
        },

        mounted() {
            this.draggable.disabled = !this.field.config.isSortable;
        },

        methods: {
            addItem() {
                this.newReplacementIds = this.generateReplacementIds(this.replacementIds, this.field.templateData.replacementIds);
                let templateFields = JSON.parse(JSON.stringify(this.field.templateData.fields));

                for (let field in templateFields) {
                    for (let id in this.newReplacementIds) {
                        if (!templateFields[field].config.isTranslatable) {
                            templateFields[field].name = templateFields[field].name.replace(id, this.newReplacementIds[id]);
                        } else {
                            let translations = templateFields[field].translations;
                            for (let translation in translations) {
                                translations[translation].name = translations[translation].name.replace(id, this.newReplacementIds[id]);
                            }
                        }
                    }
                }


                this.field.entries.push({
                    fields: templateFields
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

            remove(item, fieldName, index) {
                let alert = confirm("After clicking \"Save\", you will not be able to recover this item!");
                if (alert) {
                    if (item.id !== undefined) {
                        this.field.entries[index].fields = [{
                            type: "hidden",
                            label: "Id",
                            name: fieldName + "[" + item.id + "][remove]",
                            value: 1,
                            config: {
                                isTranslatable: false,
                            },
                            translations: []
                        }, {
                            type: "hidden",
                            label: "Id",
                            name: fieldName + "[" + item.id + "][id]",
                            value: item.id,
                            config: {
                                isTranslatable: false,
                            },
                            translations: []
                        }];
                    } else {
                        this.field.entries.splice(index, 1);
                    }
                }
            },

            toggle() {
                this.field.show = !this.field.show;
            }
        }
    }
</script>
