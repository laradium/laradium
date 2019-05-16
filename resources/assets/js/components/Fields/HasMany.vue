<template>
    <div>
        <template v-if="field.config.render_as_table">
            <has-many-table
                :field="field"
                :language="language"
                :replacement_ids="replacement_ids"/>
        </template>
        <template v-else>
            <has-many-form
                :field="field"
                :language="language"
                :replacement_ids="replacement_ids"/>
        </template>
    </div>
</template>

<script>
    import HasManyTable from './Partials/HasManyTable.vue';
    import HasManyForm from './Partials/HasManyForm.vue';

    export default {
        props: ['field', 'language', 'replacement_ids'],

        components: {
            HasManyTable,
            HasManyForm
        },
    }
</script>
