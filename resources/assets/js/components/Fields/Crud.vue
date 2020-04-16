<template>
    <form class="crud-form"
          :class="field.config.col"
          :data-name="field.name"
          method="POST"
          :action="field.url"
          @submit.prevent="onSubmit(this)">

        <input type="hidden" name="_method" :value="field.method">

        <div class="alert alert-danger" v-show="errors.length">
            <li v-for="error in errors">
                {{ error }}
            </li>
        </div>

        <div class="alert alert-success" v-show="success">
            {{ success }}
        </div>

        <row-field v-for="(row, index) in rows" :key="'row' + index" :data="row"
                   :language="current_language"></row-field>

        <div v-if="!countFieldsByType('save-buttons') && !countFieldsByType('form-submit')" class="crud-bottom">
            <div class="row">
                <div class="col-md-3">
                    <button class="btn btn-primary" :disabled="isSubmitted" @click.stop.prevent="onSubmit($el)">
                        <span v-if="isSubmitted">
                            <i class="fa fa-cog fa-spin fa-fw"></i> Saving...
                        </span>
                        <span v-else>
                            <i class="fa fa-floppy-o"></i> Save
                        </span>
                    </button>

                    <button class="btn btn-primary" @click.stop.prevent="onSubmit($el, true)"
                            :disabled="isSubmitted" v-if="!isSubmitted">
                        Save & Return
                    </button>
                </div>
                <div class="col-md-1 middle-align" v-if="field.config.is_translatable && field.config.languages">
                    Language
                </div>
                <div class="col-md-2" v-if="field.config.is_translatable && field.config.languages.length">
                    <select class="form-control language-select" v-model="current_language">
                        <option :value="language.iso_code" v-for="language in field.config.languages">
                            {{ language.iso_code }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

    </form>
</template>

<script>
    import {serverBus} from '../../laradium';

    export default {
        name: 'Crud',

        props: ['field', 'language'],

        data() {
            let current_language = '';

            if (this.field.config.is_translatable && this.field.config.languages.length) {
                current_language = this.field.config.languages[0].iso_code;
            }

            return {
                current_tab: '',
                success: '',
                is_translatable: false,
                errors: [],
                data: [],
                rows: [],
                loading: true,
                isSubmitted: false,
                current_language: current_language
            };
        },

        mounted() {
            serverBus.$on('change_language', iso => {
                this.current_language = iso;
            });

            $(this.$el).on('submit-crud', () => {
                this.onSubmit(this.$el);
            });

            let fields = this.field.fields;
            for (let field in fields) {
                if (fields.hasOwnProperty(field)) {
                    if (fields[field].type === 'row') {
                        this.rows.push(fields[field]);
                    }
                }
            }

            if (!this.rows.length) {
                this.rows.push({
                    fields: fields,
                    config: {
                        use_block: !this.field.config.without_card,
                        col: 'col-md-12'
                    }
                });
            }

            $(this.$el).on('keyup', ':input.is-invalid', (el) => {
                this.removeErrors([el.target]);
            })
        },

        methods: {
            onSubmit(form, redirect = null) {
                this.removeErrors($(form));
                this.isSubmitted = true;
                let form_data = new FormData();
                this.errors = [];
                this.success = null;
                let formButton = $('button[data-form_button="' + this.field.name + '"]');

                formButton.attr('disabled', true);

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
                    let response = res.data;
                    this.success = response.data.message;

                    $('html, body').animate({'scrollTop': $(form).offset().top - 50});

                    if (redirect) {
                        window.location = response.data.return_to;

                        return;
                    }
                    $(form).trigger('form-submitted');

                    if (response.data.redirect_to) {
                        window.location = response.data.redirect_to;
                    }

                    this.field.fields = response.data.form;
                    this.isSubmitted = false;
                    formButton.attr('disabled', false);

                }).catch(res => {
                    this.isSubmitted = false;
                    this.errors = [];
                    this.success = '';

                    let errors = res.response.data.errors;
                    this.addErrorsToTheInputs(errors);

                    if (!errors) {
                        let status = res.response.status;
                        if (status === 422 && res.response.data.message) {
                            this.errors.push(res.response.data.message);
                        } else {
                            this.errors.push('There was a technical problem with status code ' + status + ', please contact support!');
                        }
                    }

                    this.$nextTick(() => {
                        if ($(form).find('.is-invalid:first')) {
                            $('html, body').animate({'scrollTop': $(form).find('.is-invalid:first').offset().top - 50});
                        }
                    });

                    formButton.attr('disabled', false);
                    $(form).trigger('form-submitted');
                });
            },
            addErrorsToTheInputs(errors) {
                for (let error in errors) {
                    let field = this.dotsToBrackets(error);
                    let name = '[name="' + field + '"]';
                    let errorTextBlock = $('<div/>').addClass('invalid-feedback').css({display: 'block'}).text(errors[error][0]);

                    this.getFieldsByType('belongstomany').forEach(function (name) {
                        let childInput = '[name="' + name + '[crud_worker]"]';

                        if (field === name) {
                            $(this.$el)
                                .find(childInput)
                                .addClass('is-invalid')
                                .after(errorTextBlock);
                        }
                    }, this);

                    // Basic input
                    let input = $(this.$el).find(name).addClass('is-invalid');
                    input.after(errorTextBlock);
                }
            },
            removeErrors(form) {
                $(form).find('.is-invalid').each(function () {
                    $(this).removeClass('is-invalid');
                    $(this).parent().find('.invalid-feedback').remove();
                });
            },
            countFieldsByType(type, fields) {
                if (!this.data) {
                    return 0;
                }

                if (typeof fields === 'undefined') {
                    fields = this.field.fields;
                }

                let count = 0;

                fields.forEach(field => {
                    if (field.type === type) {
                        count++;
                    }

                    if (field.fields && field.fields.length) {
                        count += this.countFieldsByType(type, field.fields)
                    }
                });

                return count;
            },
            dotsToBrackets(dottedIdentifier) {
                let parts = dottedIdentifier.split('.');
                let bracketedIdentifier = parts.shift();

                for (let part of parts) {
                    bracketedIdentifier = `${bracketedIdentifier}[${part}]`;
                }

                return bracketedIdentifier;
            },
            getFieldsByType(type) {
                let fields = this.field.fields;
                let found = [];

                this.recursiveFields(found, fields, type);

                return found;
            },
            recursiveFields(found, fields, type) {
                fields.forEach(field => {
                    if (field.type === type) {
                        found.push(field.name);
                    }

                    if (field.fields) {
                        this.recursiveFields(found, field.fields, type)
                    }

                    if (field.tabs) {
                        field.tabs.forEach(tab => {
                            this.recursiveFields(found, tab.fields, type);
                        });
                    }
                });
            }
        }
    }
</script>
