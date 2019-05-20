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

        <div class="row">
            <template v-for="field in field.fields">
                <component :is="field.type + '-field'"
                           :field="field"
                           :data="field"
                           :current_tab="current_tab"
                           :language="language"
                           :replacement_ids="{}"
                ></component>
            </template>
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
                        window.location = redirect;

                        return;
                    }

                    if (typeof response.redirect !== "undefined") {
                        window.location = response.redirect;
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
        }
    }
</script>
