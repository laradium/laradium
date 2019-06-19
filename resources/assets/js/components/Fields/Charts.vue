<template>
    <div style="height: 300px;" v-bind="fieldAttributes">
        <vue-chart :data="field.chart.data"
                   :options="field.chart.options"
                   :type="field.chart.type"></vue-chart>
    </div>
</template>

<script>
    export default {
        name: 'Charts',

        props: ['field']
    }
</script>