<template>
    <div class="form-group">
        <label for="">{{ field.label }}
            <span class="badge badge-primary"
                  v-if="field.config.is_translatable">
                {{ language }}
            </span>
        </label>

        <div v-if="field.config.is_translatable">
            <input type="text"
                   v-model="item.value"
                   :name="item.name"
                   v-for="item in field.translations"
                   v-show="language === item.iso_code"
                   class="form-control" v-bind="attributes">
        </div>
        <div v-else>
            <input type="text" v-model="field.value" :name="field.name" class="form-control" v-bind="attributes">
        </div>
    </div>
</template>

<script>

    import { serverBus } from '../../laradium';

    export default {
        props: ['field', 'language', 'item'],

        computed: {
            attributes() {
                return this.field.attr;
            }
        },
        created() {
            serverBus.$on('test', function () {
               console.log(761237861236785123687);
            });
        }
    }
</script>
