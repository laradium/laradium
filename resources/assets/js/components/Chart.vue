<template>
    <canvas :id="chartId" width="1" height="1"></canvas>
</template>

<script>
    import Chart from 'chart.js';
    import axios from 'axios';

    export default {
        props: {
            type: String,
            data: Object,
            options: Object
        },

        data: function () {
            return {
                chartId: Math.random().toString(36).substring(7)
            }
        },

        mounted: function () {
            if (this.data.source === 'array') {
                this.createChart(this.data);
            } else {
                axios.get(this.data.url)
                    .then((response) => {
                        this.createChart(response.data);
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            }
        },

        methods: {
            createChart(data) {
                let chart = document.getElementById(this.chartId);
                let ctx = chart.getContext('2d');

                new Chart(ctx, {
                    type: this.type,
                    data: data,
                    options: this.options
                });
            }
        }
    }
</script>