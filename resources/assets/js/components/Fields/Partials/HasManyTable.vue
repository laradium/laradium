<template>
    <div class="border">
        <h4>
            <i class="fa fa-bars"></i> {{ field.label }}
            <span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
        </h4>

        <input type="hidden" :name="field.name + '[crud_worker]'" :value="field.value">

        <table class="table table-striped" v-if="field.entries.length">
            <thead>
            <tr>
                <th v-for="templateField in field.template_data.fields">
                    {{ templateField.label }}
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(entry, index) in field.entries">
                <template v-if="entry.config.is_deleted">
                    <td v-for="(templateField, index) in field.template_data.fields" class="deleted">
                        <span v-if="index === 0">
                            <em>Deleted</em>
                        </span>
                    </td>
                </template>

                <td v-for="(field, index) in entry.fields" :class="{'hidden': field.type === 'hidden'}">
                    <component
                        :is="field.type + '-field'"
                        :field="field"
                        :language="language"
                        :replacement_ids="new_replacement_ids"
                        :key="index"
                    ></component>
                </td>

                <td class="actions text-right">
                    <button class="btn btn-primary btn-sm"
                            @click.prevent="restore(index)"
                            v-if="entry.config.is_deleted && field.config.actions.includes('delete')">
                        <i class="fa fa-undo"></i> Restore
                    </button>
                    <button class="btn btn-danger btn-sm"
                            @click.prevent="remove(entry, field.name, index)"
                            v-if="!entry.config.is_deleted && field.config.actions.includes('delete')">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="row">
            <div class="col-md-12">
                <button
                    class="btn btn-primary btn-sm"
                    type="button"
                    @click.prevent="addItem()"
                    v-if="field.config.actions.includes('create')">

                    <i class="fa fa-plus"></i> Add {{ field.label }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import hasManyMixin from '../../../mixins/hasMany';

    export default {
        props: ['field', 'language', 'replacement_ids'],

        mixins: [hasManyMixin],

        data() {
            return {
                new_replacement_ids: {},
                removed_items: {}
            };
        },
    }
</script>

<style lang="scss">
    td {
        &.actions {
            width: 110px;
        }

        &.deleted {
            vertical-align: middle;
        }

        label {
            display: none;
        }

        .form-group {
            margin: 0;
        }
    }

    .border {
        padding: 0 10px 10px 10px;
        border-radius: 2px;
        margin: 0 0 10px;
    }
</style>
