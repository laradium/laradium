export default {
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
