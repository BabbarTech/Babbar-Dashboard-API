<template>
    <div class="chart">
        <v-chart
            ref="chart"
            class="chart"
            :autoresize="true"
            :option="option"
            downplay="remove()"
        >
        </v-chart>
    </div>
</template>

<script>
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { BarChart, LineChart } from 'echarts/charts';
import {
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent,
} from 'echarts/components';
import VChart, {THEME_KEY} from 'vue-echarts';

use([
    CanvasRenderer,
    BarChart,
    LineChart,
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent,
]);

import { mapStores } from 'pinia'
import { useDatasetCollectionStore } from '../stores/datasetCollection'

export default {
    name: 'chart',
    components: {
        VChart,
    },
    props: {
        sources: {
            type: Object,
            default: () => {}
        },
        config: {
            type: Object,
            default: () => {}
        },
        type: {
            type: String,
            required: true,
        },
        transformer: {
            type: Function,
        }
    },

    computed: {
        ...mapStores(useDatasetCollectionStore),
    },
    data: function() {
        return {
            option: null,
        }
    },

    methods: {
        init: function() {
            let option = {
                ...this.config,
                legend: {
                    data: []
                },
                series: [],
            };

            if (! option.tooltip) {
                option.tooltip = {};
            }

            this.option = option

            let vm = this;

            Object.entries(this.sources).forEach(function([name, source]) {
                vm.addSerie(name, source);
            });

        },
        addSerie(name, source) {
            this.option.legend.data.push(name)

            let data = this.transformer ? this.transformer(source) : source;

            this.option.series.push({
                name: name,
                type: this.type,
                data: data,
            });
        },

        remove(event) {
            console.log('remove');
            console.log(event);
        },
    },

    mounted () {
        console.log('chart mounted')
        let vm = this;

        this.init();

        EventBus.$on('dataset-added', function(datasetId) {
            let dataset = vm.datasetCollectionStore.datasets[datasetId];
            vm.addSerie(datasetId, dataset)
        });
    }
}
</script>

<style scoped>
.chart {
    height: 60vh;
}
</style>
