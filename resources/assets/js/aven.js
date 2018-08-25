/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import wysiwyg from "vue-wysiwyg";
window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('crud-form', require('./components/CrudForm.vue'));
Vue.component('text-field', require('./components/fields/Text.vue'));
Vue.component('hidden-field', require('./components/fields/Hidden.vue'));
Vue.component('hidden-sortable-field', require('./components/fields/HiddenSortable.vue'));
Vue.component('has-many-field', require('./components/fields/HasMany.vue'));
Vue.component('boolean-field', require('./components/fields/Boolean.vue'));
Vue.component('select-field', require('./components/fields/Select.vue'));
Vue.component('morph-to-field', require('./components/fields/MorphTo.vue'));
Vue.component('has-one-field', require('./components/fields/HasOne.vue'));
Vue.component('textarea-field', require('./components/fields/Textarea.vue'));
Vue.component('wysiwyg-field', require('./components/fields/Wysiwyg.vue'));
Vue.component('widget-constructor-field', require('./components/fields/WidgetConstructor.vue'));
Vue.component('belongs-to-many-field', require('./components/fields/BelongsToMany.vue'));
Vue.component('draggable', require('vuedraggable'));
Vue.use(wysiwyg, {});

const app = new Vue({
    el: '#app',
    data: {
        selectedPage: false
    },
    methods: {
        redirectToCreatePage() {
            window.location = '/admin/pages/create/' + this.selectedPage;
        }
    }
});
