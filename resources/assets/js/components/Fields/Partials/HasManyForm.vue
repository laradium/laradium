<template>
    <div class="border">
        <h4>
            <i class="fa fa-bars"></i> {{ field.label }}
            <span v-if="field.info"><i class="fa fa-info-circle" v-tooltip:top="field.info"></i></span>
        </h4>
        <input type="hidden" :name="field.name + '[crud_worker]'" :value="field.value">
        <draggable class="dragArea" :list="field.entries" @update="onUpdate(field.entries)" :options="draggable">
            <div v-for="(entry, index) in field.entries">
                <div class="col-md-12 border" style="border-radius: 2px; margin: 5px 5px 5px 0;">
                    <h4 class="d-inline-block">
                        <i class="mdi mdi-arrow-all handle"
                           v-if="field.config.is_sortable && !entry.config.is_deleted"></i>

                        <span v-html="entry.label"></span> <span v-if="entry.config.is_deleted"><i>Deleted</i></span>
                    </h4>

                    <div class="pull-right" style="margin-top: 7px;">
                        <button class="btn btn-success btn-sm" v-if="!entry.config.is_deleted"
                                @click.prevent="toggle(index)">
                            <span v-if="!entry.config.is_collapsed"><i class="fa fa-eye-slash"></i></span>
                            <span v-else><i class="fa fa-eye"></i></span>
                        </button>
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
                    </div>
                    <div class="row" v-show="!entry.config.is_collapsed">
                        <div v-for="(field, index) in entry.fields" :class="field.config.col">
                            <component
                                :is="field.type + '-field'"
                                :data="field"
                                :field="field"
                                :language="language"
                                :replacement_ids="new_replacement_ids"
                                :fullWidth="true"
                                :key="index"
                            ></component>
                        </div>
                    </div>

                </div>
            </div>
        </draggable>
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
                draggable: {
                    disabled: true,
                    handle: '.handle'
                },
                new_replacement_ids: {},
                removed_items: {}
            };
        },

        mounted() {
            this.draggable.disabled = !this.field.config.is_sortable;
        },

        methods: {
            onUpdate(items) {
                let i = 0;
                for (let item in items) {
                    if (!items.hasOwnProperty(item)) {
                        continue
                    }

                    let fields = items[item].fields;
                    for (let field in fields) {
                        if (!fields.hasOwnProperty(field)) {
                            continue;
                        }

                        if (fields[field].label === 'Sequence no') {
                            fields[field].value = i;
                        }
                    }
                    i++;
                }
            },

            toggle(index) {
                let config = this.field.entries[index].config;
                if (config.is_collapsed !== undefined) {
                    this.field.entries[index].config.is_collapsed = !config.is_collapsed;
                }
            }
        }
    }
</script>

<style>
    .border {
        padding: 0 10px 10px 10px;
        border-radius: 2px;
        margin: 0 0 10px;
    }
</style>
