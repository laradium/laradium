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

                <div class="row">
                    <div v-if="data.tabs.length > 1" class="col-md-12">
                        <ul class="nav nav-tabs" v-if="data.tabs.length > 1">
                            <li class="nav-item" v-for="(tab, index) in data.tabs">
                                <a :href="'#' + tab" data-toggle="tab" aria-expanded="false" class="nav-link"
                                   :class="{'active': index === 0}">
                                    {{ tab }}
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade show" :class="{'active': index === 0}" :id="tab"
                                 v-for="(tab, index) in data.tabs">
                                <div v-for="input in data.inputs" v-if="input.tab == tab">
                                    <component :is="input.type + '-field'"
                                               :input="input"
                                               :language="language"
                                               :replacementIds="{}"
                                    ></component>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="col-md-12">
                        <div v-for="input in data.inputs"
                             class="col-md-12">
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
                        <div class="col-md-1 middle-align" v-if="data.isTranslatable && data.languages.length">
                            Language
                        </div>
                        <div class="col-md-2" v-if="data.isTranslatable && data.languages.length">

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
                loading: true
            };
        },
        created() {
            axios({
                'method': 'GET',
                'url': url
            }).then(res => {
                this.data = res.data;
                for (let language in this.data.languages) {
                    if (this.data.languages.hasOwnProperty(language)) {
                        if (this.data.languages[language].is_current) {
                            this.language = this.data.languages[language].iso_code;
                        }
                    }
                }
            });
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
                $(form).find('input[name][type!="file"], select[name], textarea[name]').each(function(i, e) {
                    if ($(e).attr('type') === 'checkbox' || $(e).attr('type') === 'radio') {
                        if ($(e).is(':checked')) {
                            formData.append($(e).attr('name'), $(e).val());
                        }
                    } else {
                        formData.append($(e).attr('name'), $(e).val());
                    }
                });

                $(form).find('input[name][type="file"]').each(function(i, e) {
                    if ($(e)[0].files.length > 0) {
                        formData.append($(e).attr('name'), $(e)[0].files[0]);
                    }
                });

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

                    if(!errors) {
                        let status = res.response.status;
                        this.errors.push('There was a technical problem with status code ' + status + ', please contact technical staff!');
                    }
                });

            }
        }
    }
</script>
