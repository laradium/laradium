<template>
    <div :class="data.config.col ? data.config.col : 'col-md-12'" v-bind="fieldAttributes">
        <div class="row" v-if="tabs.length">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li class="nav-item" v-for="(tab, index) in tabs">
                        <a :href="'#tab-' + tab.slug" data-toggle="tab" @click.prevent="current_tab = tab.slug"
                           aria-expanded="false" class="nav-link"
                           :class="{'active': index === 0}">
                            {{ tab.name }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div :class="{ 'tab-content': tabs.length, 'col-md-12': tabs.length, 'row': !tabs.length}" :style="{'margin-bottom:20px': tabs.length}">
            <div v-for="field in data.fields" :class="field.config.col ? field.config.col : 'col-md-12'">
                <component :is="field.type + '-field'"
                           :field="field"
                           :data="field"
                           :current_tab="current_tab"
                           :language="language"
                           :replacement_ids="{}"
                ></component>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['data', 'language'],

        data() {
            return {
                current_tab: '',
                tabs: [],
            };
        },

        created() {
            let fields = this.data.fields;
            let i = 0;
            for (let field in fields) {
                if (fields.hasOwnProperty(field)) {
                    if (fields[field].type === 'tab') {
                        if (i === 0) {
                            this.current_tab = fields[field].slug;
                        }
                        this.tabs.push({
                            slug: fields[field].slug,
                            name: fields[field].name,
                        });
                    }
                    i++;
                }
            }
        },

    }
</script>