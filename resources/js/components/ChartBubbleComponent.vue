<template>
    <div class="container">
        <slot name="loader" :loading="loading">
            <div class="loading" v-if="loading"></div>
        </slot>

        <Bubble
            class=""
            ref="chart"
            v-if="chartData"
            :chart-data="chartData"
            :chart-options="chartOptions"
            :chart-id="chartId"
            :dataset-id-key="datasetIdKey"
            :plugins="plugins"
            :css-classes="cssClasses"
            :styles="styles"
            :width="width"
            :height="height"
        />
    </div>
</template>

<script>
import { Bubble } from 'vue-chartjs/legacy'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    PointElement,
    LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, PointElement, LinearScale)

export default {
    name: 'BubbleChart',
    components: { Bubble },
    props: {
        chartId: {
            type: String,
            default: 'bar-chart'
        },
        datasetIdKey: {
            type: String,
            default: 'label'
        },
        width: {
            type: Number,
            default: 400
        },
        height: {
            type: Number,
            default: 400
        },
        cssClasses: {
            default: '',
            type: String
        },
        styles: {
            type: Object,
            default: () => {}
        },
        plugins: {
            type: Object,
            default: () => {}
        },
        source: {
            type: Array,
        },
        selected: {
            type: Object,
        },
        colorPrimary: {
            type: String,
            default: '#4dc9f6'
        },
        colorSecondary: {
            type: String,
            default: '#dc3444'
        },
        loading: {
            type: Boolean,
            default: false
        }
    },
    data: function () {
        return {
            chartOptions: this.getChartOptions(),
            chartData: null,
        }
    },
    watch: {
        // whenever Source changes, this function will run
        source(newSource, oldSource) {
            this.chartData = this.getData()
        }
    },

    methods: {
        hexToRGB: function (hex, alpha) {
            let r = parseInt(hex.slice(1, 3), 16),
                g = parseInt(hex.slice(3, 5), 16),
                b = parseInt(hex.slice(5, 7), 16);

            if (alpha) {
                return "rgba(" + r + ", " + g + ", " + b + ", " + alpha + ")";
            } else {
                return "rgb(" + r + ", " + g + ", " + b + ")";
            }
        },
    },
    mounted() {
        this.chartData = this.getData();
    }
}
</script>
