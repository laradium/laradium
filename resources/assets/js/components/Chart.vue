<template>
    <div :style="styleObject">
        <canvas :id="chartId" width="1" height="1"></canvas>
    </div>
</template>

<script>
    import Chart from 'chart.js';

    export default {
        props: {
            type: String,
            data: Object,
            options: Object,
            height: String
        },

        data: function () {
            return {
                chartId: Math.random().toString(36).substring(7),
                styleObject: {
                    width: '100%',
                    height: this.height
                },
            }
        },

        mounted: function () {
            let chart = document.getElementById(this.chartId);
            let ctx = chart.getContext('2d');
            new Chart(ctx, {
                type: this.type,
                data: this.data,
                options: this.options
            });
        }
    }
</script>