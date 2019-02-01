/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.resizable = require('jquery-resizable-dom');
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('crud-form', require('./components/CrudForm.vue').default);
Vue.component('text-field', require('./components/fields/Text.vue').default);
Vue.component('textarea-field', require('./components/fields/Textarea.vue').default);
Vue.component('boolean-field', require('./components/fields/Boolean.vue').default);
Vue.component('tab-field', require('./components/fields/Tab.vue').default);
Vue.component('hidden-field', require('./components/fields/Hidden.vue').default);
Vue.component('select-field', require('./components/fields/Select.vue').default);
Vue.component('select2-field', require('./components/fields/Select2.vue').default);
Vue.component('svgicon-field', require('./components/fields/SvgIcon.vue').default);
Vue.component('file-field', require('./components/fields/File.vue').default);
Vue.component('email-field', require('./components/fields/Email.vue').default);
Vue.component('password-field', require('./components/fields/Password.vue').default);
Vue.component('radio-field', require('./components/fields/Radio.vue').default);
Vue.component('date-field', require('./components/fields/Date.vue').default);
Vue.component('datetime-field', require('./components/fields/DateTime.vue').default);
Vue.component('time-field', require('./components/fields/Time.vue').default);
Vue.component('wysiwyg-field', require('./components/fields/Wysiwyg.vue').default);
Vue.component('color-field', require('./components/fields/Color.vue').default);
Vue.component('row-field', require('./components/fields/Row.vue').default);
Vue.component('block-field', require('./components/fields/Block.vue').default);
Vue.component('col-field', require('./components/fields/Col.vue').default);
Vue.component('save-buttons-field', require('./components/fields/SaveButtons.vue').default);
Vue.component('language-selector-field', require('./components/fields/LanguageSelect.vue').default);
Vue.component('link-field', require('./components/fields/Link.vue').default);
Vue.component('button-field', require('./components/fields/Button.vue').default);

Vue.component('hasone-field', require('./components/fields/HasOne.vue').default);
Vue.component('hasmany-field', require('./components/fields/HasMany.vue').default);
Vue.component('belongsto-field', require('./components/fields/BelongsTo.vue').default);
Vue.component('belongstomany-field', require('./components/fields/BelongsToMany.vue').default);
Vue.component('morphto-field', require('./components/fields/MorphTo.vue').default);
Vue.component('widgetconstructor-field', require('./components/fields/WidgetConstructor.vue').default);

Vue.component('select2', require('./components/Select2.vue').default);
Vue.component('draggable', require('vuedraggable'));
Vue.component('tree-field', require('./components/fields/Tree.vue').default);
Vue.component('vue-menu', require('./components/fields/Menu.vue').default);
Vue.component('menuitems', require('./components/fields/MenuItems.vue').default);
Vue.component('js-tree', require('./components/fields/JsTree.vue').default);

require('./misc/import-form')

if (typeof window.laradiumFields === 'undefined') {
    window.laradiumFields = {};
}

for (let key in window.laradiumFields) {
    if (window.laradiumFields.hasOwnProperty(key)) {
        Vue.component(key.split(/(?=[A-Z])/).join('').toLowerCase() + '-field', window.laradiumFields[key])
    }
}

// Trumbowyg
import VueTrumbowyg from 'vue-trumbowyg';
import 'trumbowyg/dist/plugins/upload/trumbowyg.upload.min';
import 'trumbowyg/dist/plugins/table/trumbowyg.table.min';
import 'trumbowyg/dist/plugins/colors/trumbowyg.colors.min';

require('./trumbowyg/plugins/noembed/trumbowyg.noembed');
require('./trumbowyg/plugins/cleanpaste/trumbowyg.cleanpaste');
import 'trumbowyg/dist/plugins/resizimg/trumbowyg.resizimg.min';
import 'trumbowyg/dist/plugins/fontsize/trumbowyg.fontsize.min';
import 'trumbowyg/dist/plugins/fontfamily/trumbowyg.fontfamily.min';
import 'trumbowyg/dist/plugins/lineheight/trumbowyg.lineheight.min';
import 'trumbowyg/dist/plugins/history/trumbowyg.history.min';

$.trumbowyg.svgPath = '/laradium/admin/assets/images/trumbowyg/icons.svg';
Vue.use(VueTrumbowyg);

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

            return {
                id: rand_id,
                replacement_ids: replacement_ids
            };
        }
    },

    computed: {
        fieldAttributes() {
            let attributes = {};

            if (this.field && this.field.attr) {
                for (let key in this.field.attr) {
                    if (this.field.attr.hasOwnProperty(key) && isNaN(parseInt(key))) {
                        attributes[key] = this.field.attr[key];
                    }
                }
            }

            if (this.data && this.data.attr) {
                for (let key in this.data.attr) {
                    if (this.data.attr.hasOwnProperty(key) && isNaN(parseInt(key))) {
                        attributes[key] = this.data.attr[key];
                    }
                }
            }

            return attributes;
        }
    }
});

Vue.directive('tooltip', {
    bind: bsTooltip,
    update: bsTooltip,
    unbind(el, binding) {
        $(el).tooltip('destroy');
    }
});

function bsTooltip(el, binding) {
    let trigger = 'hover';
    if (binding.modifiers.focus || binding.modifiers.hover || binding.modifiers.click) {
        const t = [];
        if (binding.modifiers.focus) t.push('focus');
        if (binding.modifiers.hover) t.push('hover');
        if (binding.modifiers.click) t.push('click');
        trigger = t.join(' ');
    }

    $(el).tooltip({
        title: binding.value,
        placement: binding.arg,
        trigger: trigger,
        html: binding.modifiers.html ? binding.modifiers.html : false
    });
}

export const serverBus = new Vue();

const app = new Vue({
    el: '#crud-form',
    data: {
        selectedPage: false
    },
    methods: {
        redirectToCreatePage() {
            window.location = '/admin/pages/create?channel=' + this.selectedPage;
        }
    }
});