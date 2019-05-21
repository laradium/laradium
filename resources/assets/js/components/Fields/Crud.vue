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
                   :language="data.default_language"></row-field>

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
        name: 'Crud',

        props: ['field', 'language'],

        data() {
            return {
                current_tab: '',
                success: '',
                is_translatable: false,
                errors: [],
                data: [],
                rows: [],
                loading: true,
                isSubmitted: false
            };
        },

        mounted() {
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
                        use_block: true,
                        col: 'col-md-12'
                    }
                });
            }
        },

        methods: {
            onSubmit(form, redirect = null) {
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

                    $('html, body').animate({'scrollTop': $(form).find('.alert.alert-danger').offset().top - 50});

                    if (redirect) {
                        window.location = response.data.return_to;

                        return;
                    }

                    if (response.data.redirect_to) {
                        window.location = response.data.redirect_to;
                    }

                    this.data = response.data;
                    this.isSubmitted = false;
                    formButton.attr('disabled', false);

                }).catch(res => {
                    this.isSubmitted = false;
                    this.errors = [];
                    this.success = '';

                    let errors = res.response.data.errors;

                    for (let error in errors) {
                        if (errors.hasOwnProperty(error)) {
                            this.errors.push(errors[error][0]);
                        }
                    }

                    if (!errors) {
                        let status = res.response.status;
                        this.errors.push('There was a technical problem with status code ' + status + ', please contact technical staff!');
                    }

                    this.$nextTick(() => {
                        $('html, body').animate({'scrollTop': $(form).find('.alert.alert-danger').offset().top - 50});
                    });

                    formButton.attr('disabled', false);
                });
            },
            hasBlockWrapper() {
                let blockFields = _.filter(this.field.fields, (field) => {
                    if(field.type === 'block') {
                        return true;
                    }
                });

                return blockFields.length;
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
            }
        }
    }
</script>
