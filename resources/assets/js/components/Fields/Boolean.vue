<template>
    <div>
        <div class="form-group">
            <br>
            <div class="checkbox checkbox-primary">
                <input type="hidden" value="0" :name="field.name">
                <input type="checkbox" value="1" v-model="field.checked" :name="field.name" :id="field.name">
                <label :for="field.name">
                    {{ field.label }}
                </label>
            </div>
        </div>
        <div class="row" v-show="field.checked && field.fields.length">
            <div v-for="(newField, index) in field.fields" :class="newField.config.col">
                <component
                        :is="newField.type + '-field'"
                        :field="newField"
                        :language="language"
                        :key="index"
                ></component>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language', 'replacement_ids'],

        data() {
            return {
                new_replacement_ids: {}
            };
        },

        mounted() {
            /*console.log(this.field.template_data.fields);
            console.log(this.replacement_ids);
            let generate_replacements = this.generateReplacementIds(this.replacement_ids, this.field.template_data.replacement_ids);
            this.new_replacement_ids = generate_replacements.replacement_ids;
            let template_fields = _.cloneDeep(this.field.template_data.fields);

            for (let field in template_fields) {
                for (let id in this.replacement_ids) {
                    if (!template_fields[field].config.is_translatable) {
                        template_fields[field].name = template_fields[field].name.replace(id, this.replacement_ids[id]);
                    } else {
                        let translations = template_fields[field].translations;
                        for (let translation in translations) {
                            translations[translation].name = translations[translation].name.replace(id, this.replacement_ids[id]);
                        }
                    }
                }
            }

            this.field.fields = template_fields;*/
        },
    }
</script>
