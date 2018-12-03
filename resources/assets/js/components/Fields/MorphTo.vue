<template>
    <div>
        <div v-for="(field, index) in field.fields" :class="field.config.col">
            <component
                    :is="field.type + '-field'"
                    :field="field"
                    :language="language"
                    :replacement_ids="new_replacement_ids"
                    :key="index"
            ></component>
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
            if(!this.field.exists) {
                let generate_replacements = this.generateReplacementIds(this.replacement_ids, this.field.replacement_ids);
                this.new_replacement_ids = generate_replacements.replacement_ids;

                let fields = this.field.fields;

                for (let field in fields) {
                    for (let id in this.new_replacement_ids) {
                        if (!fields[field].config.is_translatable) {
                            fields[field].name = fields[field].name.replace(id, this.new_replacement_ids[id]);
                        } else {
                            let translations = fields[field].translations;
                            for (let translation in translations) {
                                translations[translation].name = translations[translation].name.replace(id, this.new_replacement_ids[id]);
                            }
                        }
                    }
                }
            }
        },
    }
</script>
