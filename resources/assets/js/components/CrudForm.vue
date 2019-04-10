<template>
    <form class="crud-form"
          :action="url"
          method="POST"
          @submit.prevent="onSubmit($event)">

        <input type="hidden" name="_method" :value="method" v-if="method">

        <div class="alert alert-danger" v-if="errors.length">
            <li v-for="error in errors">
                {{ error }}
            </li>
        </div>

        <div class="alert alert-success" v-if="success">
            {{ success }}
        </div>

        <row-field v-for="(row, index) in rows" :key="'row' + index" :data="row"
                   :language="data.default_language"></row-field>

        <div v-if="!countFieldsByType('save-buttons')" class="crud-bottom">
            <div class="row">
                <div class="col-md-3">
                    <button class="btn btn-primary" :disabled="isSubmitted">
                        <span v-if="isSubmitted">
                            <i class="fa fa-cog fa-spin fa-fw"></i> Saving...
                        </span>
                        <span v-else>
                            <i class="fa fa-floppy-o"></i> Save
                        </span>
                    </button>

                    <button class="btn btn-primary"
                            @click.stop.prevent="onSubmit($event, data.actions.index)"
                            :disabled="isSubmitted"
                            v-if="!isSubmitted">
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
        name: 'CrudForm',

        props: ['url', 'method', 'form_data'],

        data() {
            return {
                language: '',
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

        created() {
            this.data = JSON.parse(this.form_data).data;

            let fields = this.data.form;
            for (let field in fields) {
                if (fields.hasOwnProperty(field)) {
                    if (fields[field].type === 'row') {
                        this.rows.push(fields[field]);
                    }
                }
            }

            if (!this.rows.length) {
                this.rows.push({
                    fields: this.data.form,
                    config: {
                        use_block: true,
                        col: 'col-md-12'
                    }
                });
            }
        },

        methods: {
            onSubmit(el, redirect) {
                this.isSubmitted = true;
                let form = el.target;
                let form_data = new FormData();
                this.errors = [];
                this.success = null;

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
                    $('html, body').animate({'scrollTop': $(form).find('.alert.alert-danger').offset().top - 50});
                    this.success = res.data.success;

                    if (redirect) {
                        window.location = redirect;

                        return;
                    }

                    if (typeof res.data.redirect !== "undefined") {
                        window.location = res.data.redirect;
                    }

                    this.data = res.data;
                    this.isSubmitted = false;

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
                });

            },

            countFieldsByType(type, fields) {
                if (!this.data) {
                    return 0;
                }

                if (typeof fields === 'undefined') {
                    fields = this.data.form;
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
