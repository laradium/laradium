
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import { VueEditor } from "vue2-editor";

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('crud-form', require('./components/CrudForm.vue'));
Vue.component('text-field', require('./components/fields/Text.vue'));
Vue.component('textarea-field', require('./components/fields/Textarea.vue'));
Vue.component('boolean-field', require('./components/fields/Boolean.vue'));
Vue.component('tab-field', require('./components/fields/Tab.vue'));
Vue.component('hidden-field', require('./components/fields/Hidden.vue'));
Vue.component('select-field', require('./components/fields/Select.vue'));
Vue.component('file-field', require('./components/fields/File.vue'));
Vue.component('email-field', require('./components/fields/Email.vue'));
Vue.component('password-field', require('./components/fields/Password.vue'));
Vue.component('radio-field', require('./components/fields/Radio.vue'));
Vue.component('date-field', require('./components/fields/Date.vue'));
Vue.component('datetime-field', require('./components/fields/DateTime.vue'));
Vue.component('time-field', require('./components/fields/Time.vue'));
Vue.component('wysiwyg-field', require('./components/fields/Wysiwyg.vue'));

Vue.component('hasone-field', require('./components/fields/HasOne.vue'));
Vue.component('hasmany-field', require('./components/fields/HasMany.vue'));
Vue.component('belongsto-field', require('./components/fields/BelongsTo.vue'));

Vue.component('draggable', require('vuedraggable'));
Vue.component('VueEditor', VueEditor);

Vue.mixin({
    methods: {
        generateReplacementIds(replacement_ids, replacement_id_list_old) {
            let rand_id = Math.random().toString(36).substring(7);
            let replacement_id_list = _.cloneDeep(replacement_id_list_old);

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