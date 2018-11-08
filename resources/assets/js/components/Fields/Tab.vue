<template>
    <div role="tabpanel" class="row tab-pane fade show"
         :class="{ active: current_tab === field.slug}"
         :id="'tab-' + field.slug"
         v-show="current_tab === field.slug"
    >
        <div v-for="field in field.fields" :class="field.config.col">
            <component :is="field.type + '-field'"
                       :field="field"
                       :language="language"
                       :replacement_ids="{}"
            ></component>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field', 'language', 'item', 'current_tab', 'replacement_ids'],

        data() {
            return {
                tabs: ['main', 'other tab']
            };
        },

        computed: {
            attributes() {
                return this.field.attr;
            }
        }
    }
</script>
