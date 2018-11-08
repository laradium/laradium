<template>
    <form class="crud-form"
          :action="url"
          method="POST"
          @submit.prevent="onSubmit(this)">

        <input type="hidden" name="_method" :value="method" v-if="method">

        <div class="alert alert-danger" v-show="errors.length">
            <li v-for="error in errors">
                {{ error }}
            </li>
        </div>

        <div class="alert alert-success" v-if="success">
            {{ success }}
        </div>
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
        <div :class="{ 'tab-content': tabs.length, 'col-md-12': tabs.length, 'row': !tabs.length }">
            <div v-for="field in data.form" :class="field.config.col">
                <component :is="field.type + '-field'"
                           :field="field"
                           :current_tab="current_tab"
                           :language="data.default_language"
                           :replacement_ids="{}"
                ></component>
            </div>
        </div>

        <div class="crud-bottom">
            <div class="row">
                <div class="col-md-2">
                    <button class="btn btn-primary">
                        Save
                    </button>
                </div>
                <div class="col-md-1 middle-align" v-if="data.is_translatable && data.languages">
                    Language
                </div>
                <div class="col-md-2" v-if="data.is_translatable && data.languages.length">
                    <select class="form-control language-select" v-model="data.default_language">
                        <option :value="language.iso_code" v-for="language in data.languages">
                            {{ language.iso_code }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
    export default {
        props: ['url', 'method'],
        data() {
            return {
                language: '',
                current_tab: '',
                success: '',
                is_translatable: false,
                errors: [],
                data: [],
                tabs: [],
                loading: true
            };
        },
        created() {
            let data = document.getElementsByName('data');
            this.data = JSON.parse(data[0].value).data;

            let fields = this.data.form;
            let i = 0;
            for (let field in fields) {
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


            console.log(this.tabs);
        },
        methods: {
            onSubmit(el) {
                let form = document.getElementsByClassName('crud-form')[0];
                let form_data = new FormData();
                /*
                 * Fix for safari FormData bug
                 */
                $(form).find('input[name][type!="file"], select[name], textarea[name]').each(function (i, e) {
                    if ($(e).attr('type') === 'checkbox' || $(e).attr('type') === 'radio') {
                        if ($(e).is(':checked')) {
                            form_data.append($(e).attr('name'), $(e).val());
                        }
                    } else {
                        form_data.append($(e).attr('name'), $(e).val());
                    }
                });

                $(form).find('input[name][type="file"]').each(function (i, e) {
                    if ($(e)[0].files.length > 0) {
                        form_data.append($(e).attr('name'), $(e)[0].files[0]);
                    }
                });

                let url = form.getAttribute('action');
                axios({
                    method: 'POST',
                    url: url,
                    data: form_data
                }).then(res => {
                    // this.data = res.data.data;
                    this.errors = [];
                    $('html, body').animate({'scrollTop': $('.alert.alert-danger').offset().top - 50});
                    this.success = res.data.success;

                    if (res.data.redirect != undefined) {
                        window.location = res.data.redirect;
                    }

                }).catch(res => {
                    this.errors = [];
                    this.success = '';
                    let errors = res.response.data.errors;

                    $('html, body').animate({'scrollTop': $('.alert.alert-danger').offset().top - 50});

                    for (let error in errors) {
                        this.errors.push(errors[error][0]);
                    }

                    if (!errors) {
                        let status = res.response.status;
                        this.errors.push('There was a technical problem with status code ' + status + ', please contact technical staff!');
                    }
                });

            }
        }
    }
</script>
