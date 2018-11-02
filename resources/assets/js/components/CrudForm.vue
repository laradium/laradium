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
        <div class="row">
            <div v-for="field in data.form" :class="field.config.col">
                <component :is="field.type + '-field'"
                           :field="field"
                           :language="data.default_language"
                           :replacement_ids="{}"
                ></component>
            </div>
        </div>

        <button class="btn btn-primary">
            Save
        </button>
    </form>
</template>

<script>
    export default {
        props: ['url', 'method'],
        data() {
            return {
                language: '',
                success: '',
                is_translatable: false,
                errors: [],
                data: [],
                loading: true
            };
        },
        created() {
            let data = document.getElementsByName('data');
            this.data = JSON.parse(data[0].value).data;
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
