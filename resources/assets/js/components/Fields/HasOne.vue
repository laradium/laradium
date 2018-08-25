<template>
    <transition name="fade">
        <div>
            <div v-for="input in input.fields">
                <component :is="input.type + '-field'"
                           :input="input"
                           :replacementIds="replacementIds"
                           :language="language">
                </component>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        props: ['input', 'language'],
        data() {
            return {
                replacementIds: {}
            };
        },
        mounted() {
            if(this.input.id) {
                let id = this.input.id;
                let fields = this.input.fields;
                for (let field in fields) {
                    let repAttr = fields[field].replacemenetAttributes;

                    for(let ids in repAttr) {
                        if(!this.replacementIds[repAttr[ids]]) {
                            this.replacementIds[repAttr[ids]] = Math.random().toString(36).substring(7);
                        }
                    }

                    if(fields[field].isTranslatable) {
                        for (let attribute in fields[field].translatedAttributes) {
                            for(id in this.replacementIds) {
                                fields[field].translatedAttributes[attribute].name = fields[field].translatedAttributes[attribute].name.replace(id, this.replacementIds[id]);
                            }
                        }
                    } else {
                        for(id in this.replacementIds) {
                            fields[field].name = fields[field].name.replace(id, this.replacementIds[id]);
                        }
                    }

                }
            }
        },
    }
</script>
