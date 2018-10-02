<template>
    <div>
        <transition-group name="fade">
            <div class="loader" key="on" v-if="loading"></div>
            <form key="1" :action="url" method="post" @submit.prevent="onSubmit(this)" class="form-horizontal crud-form"
                  v-if="data.languages.length">

                <div class="alert alert-danger" v-show="errors.length">
                    <li v-for="error in errors">
                        {{ error }}
                    </li>
                </div>

                <div class="alert alert-success" v-if="success">
                    {{ success }}
                </div>

                <input type="hidden" name="_method" :value="method" v-if="method">

                <div v-if="data.tabs.length > 1" class="col-md-12">
                    <ul class="nav nav-tabs" v-if="data.tabs.length > 1">
                        <li class="nav-item" v-for="(tab, index) in data.tabs">
                            <a :href="'#tab-' + index" data-toggle="tab" aria-expanded="false" class="nav-link"
                               :class="{'active': index === 0}">
                                {{ tab }}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade show" :class="{'active': index === 0}"
                             :id="'tab-' + index"
                             v-for="(tab, index) in data.tabs">
                            <div class="row">
                                <div v-for="input in data.inputs" v-if="input.tab === tab"
                                     :class="'col-' + input.col.type + '-' + input.col.size">
                                    <component :is="input.type + '-field'"
                                               :input="input"
                                               :language="language"
                                               :replacementIds="{}"
                                    ></component>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="row">
                        <div v-for="input in data.inputs"
                             :class="'col-' + input.col.type + '-' + input.col.size">
                            <component :is="input.type + '-field'"
                                       :input="input"
                                       :language="language"
                                       :replacementIds="{}"
                            ></component>
                        </div>
                    </div>
                </div>

                <div class="crud-bottom">
                    <div class="row">
                        <div class="col-md-2">
                            <button class="btn btn-primary">
                                Save
                            </button>
                        </div>
                        <div class="col-md-2" v-if="data.isTranslatable && data.languages.length">
                            Language
                            <select class="form-control language-select" v-model="language">
                                <option :value="language.iso_code" v-for="language in data.languages">
                                    {{ language.iso_code }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

            </form>
        </transition-group>
    </div>
</template>

<script>
    export default {
        props: ['url', 'method'],

        data() {
            return {
                language: '',
                success: '',
                isTranslatable: false,
                errors: [],
                data: {
                    languages: [],
                    tabs: []
                },
                loading: true,
                fallbackLanguage: false
            };
        },

        created() {
            axios({
                'method': 'GET',
                'url': url
            }).then(res => {
                this.data = res.data;
                this.setLanguage();
            });

            this.$eventHub.$on('change-input', this.changeInput);
            this.$eventHub.$on('change-languages', this.changeLanguages);
        },

        mounted() {
            this.loading = false;
        },

        methods: {
            onSubmit(el) {
                let form = document.getElementsByClassName('crud-form')[0];
                let formData = new FormData();
                /*
                 * Fix for safari FormData bug
                 */
                $(form).find('input[name][type!="file"], select[name], textarea[name]').each(function (i, e) {
                    if ($(e).attr('type') === 'checkbox' || $(e).attr('type') === 'radio') {
                        if ($(e).is(':checked')) {
                            formData.append($(e).attr('name'), $(e).val());
                        }
                    } else {
                        formData.append($(e).attr('name'), $(e).val() ? $(e).val() : '');
                    }
                });

                $(form).find('input[name][type="file"]').each(function (i, e) {
                    if ($(e)[0].files.length > 0) {
                        formData.append($(e).attr('name'), $(e)[0].files[0]);
                    }
                });

                if (this.fallbackLanguage) {
                    formData.append('language', this.fallbackLanguage)
                }

                let url = form.getAttribute('action');
                axios({
                    method: 'POST',
                    url: url,
                    data: formData
                }).then(res => {
                    this.data = res.data.data;
                    this.errors = [];
                    $('html, body').animate({'scrollTop': $('.alert.alert-danger').offset().top - 50});
                    this.success = res.data.success;

                    if (res.data.redirect !== undefined) {
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
                        this.errors.push('There was a technical problem with status code ' + status + ', please, contact technical staff!');
                    }
                });
            },

            setLanguage() {
                for (let language in this.data.languages) {
                    if (this.data.languages.hasOwnProperty(language)) {
                        if (this.data.languages[language].is_current) {
                            this.language = this.data.languages[language].iso_code;
                        }
                    }
                }
            },

            changeInput(inputData) {
                let input = this.findInput(inputData.name);
                if (input && input.type === 'select') {
                    input.value = '';
                    input.options = inputData.options;
                }
            },

            changeLanguages(languages) {
                let self = this;
                this.data.languages = languages;
                this.setLanguage();

                $.each(languages, function (index, language) {
                    if (language.is_fallback) {
                        self.fallbackLanguage = language.iso_code;
                    }
                })
            },

            findInput(name) {
                for (let i = 0, len = this.data.inputs.length; i < len; i++) {
                    if (this.data.inputs[i].fields) {
                        for (let j = 0, length = this.data.inputs[i].fields.length; j < length; j++) {
                            if (this.data.inputs[i].fields[j].name === this.data.inputs[i].name.toLowerCase() + '[' + name + ']')
                                return this.data.inputs[i].fields[j];
                        }
                    }

                    if (this.data.inputs[i].name === name)
                        return this.data.inputs[i];
                }

                return null;
            }
        }
    }
</script>
