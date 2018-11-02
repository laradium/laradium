
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('crud-form', require('./components/CrudForm.vue'));
Vue.component('text-field', require('./components/fields/Text.vue'));
Vue.component('boolean-field', require('./components/fields/Boolean.vue'));
Vue.component('hasmany-field', require('./components/fields/HasMany.vue'));
Vue.component('hidden-field', require('./components/fields/Hidden.vue'));
Vue.component('draggable', require('vuedraggable'));

Vue.mixin({
    methods: {
        generateReplacementIds(replacement_ids, replacement_id_list_old) {
            let rand_id = Math.random().toString(36).substring(7);
            let replacement_id_list = JSON.parse(JSON.stringify(replacement_id_list_old));

            let lastId = '';
            for (let repId in replacement_id_list) {
                if (!replacement_ids[replacement_id_list[repId]]) {
                    replacement_ids[replacement_id_list[repId]] = Math.random().toString(36).substring(7);
                }

                lastId = replacement_id_list[repId];
            }

            if (lastId) {
                replacement_ids[lastId] = rand_id;
            }

            return replacement_ids;
        }
    }
});

const app = new Vue({
    el: '#crud-form',
    data: {
        selectedPage: false
    },
    methods: {
        redirectToCreatePage() {
            window.location = '/admin/pages/create/' + this.selectedPage;
        }
    }
});