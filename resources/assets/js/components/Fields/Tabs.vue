<template>
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" v-for="tab in data.tabs">
                <a class="nav-link"
                   :class="{ active: active === tab.slug}"
                   data-toggle="tab"
                   :href="'#' + tab.slug"
                   role="tab"
                   :aria-controls="tab.name"
                   aria-selected="true"
                   v-html="tab.name"
                   @click="setActive(tab)"
                ></a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade" :class="{ active: active === tab.slug, show: active === tab.slug}"
                 :id="tab.slug"
                 role="tabpanel"
                 v-for="tab in data.tabs">
                <div v-for="field in tab.fields" :class="field.config.col">
                    <component :is="field.type + '-field'"
                               :field="field"
                               :data="field"
                               :language="language"
                               :replacement_ids="{}"
                    ></component>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['data', 'language'],

        data() {
            return {
                active: null
            };
        },

        mounted() {
            let hash = window.location.hash;
            let tab = _.filter(this.data.tabs, (tab) => {
                return tab.slug === _.replace(hash,new RegExp('#','g'),'');
            });


            if(tab.length) {
                this.setActive(tab[0]);
            }

            this.active = this.getActive();
        },

        methods: {
            getStorageKey() {
                return 'tab-' + _.map(this.data.tabs, 'slug').join('-');
            },
            setActive(tab) {
                localStorage.setItem(this.getStorageKey(), tab.slug);

                return tab.slug;
            },
            getActive() {
                let activeFromStorage = localStorage.getItem(this.getStorageKey());
                if (!activeFromStorage) {
                    return this.setActive(this.data.tabs[0]);
                }

                return activeFromStorage;
            }
        }
    }
</script>
