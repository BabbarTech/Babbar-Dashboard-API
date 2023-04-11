<script>
import ChartBubbleComponent from '../components/ChartBubbleComponent'
const interpolate = require('color-interpolate');

export default {
    name: 'BacklinkBubbleChart',
    extends: ChartBubbleComponent,

    computed: {
        maxSourceKeywordsInTop20: function() {
            return Math.max(...this.source.map(item => item.source_nb_keywords_in_top20))
        },
        minSourceKeywordsInTop20: function() {
            return Math.min(...this.source.map(item => item.source_nb_keywords_in_top20))
        },
    },
    methods: {
        getColor: function(context, alpha)
        {
            if (context.raw?.source_nb_keywords_in_top20 < 1) {
                return '#cccccc' + alpha;
            }

            let nbKeywords = context.raw?.source_nb_keywords_in_top20 || 0;
            let ratio = (nbKeywords - this.minSourceKeywordsInTop20) / (this.maxSourceKeywordsInTop20 - this.minSourceKeywordsInTop20)

            if (isNaN(ratio) || ratio > 1) {
                ratio = 1;
            }

            let colormap = interpolate([
                this.colorPrimary + alpha,
                '#ac3cf8' + alpha,
                this.colorSecondary + alpha,
            ]);

            return colormap(ratio);
        },
        getChartOptions: function() {
            let vm = this;
            return {
                responsive: true,

                elements: {
                    point: {
                        borderColor: function(context) {
                            if (vm.selected && context.raw.id === vm.selected.id) {
                                return vm.colorSecondary;
                            }

                            if (context.raw?.source_nb_keywords_in_top20 < 1) {
                                return '#bbbbbbdd';
                            }

                            return vm.getColor(context, 'aa');
                        },
                        borderWidth: function(context) {
                            if (vm.selected && context.raw.id === vm.selected.id) {
                                return 3;
                            }

                            return 1;
                        },
                        hoverBackgroundColor: 'transparent',
                        hoverBorderColor: this.colorSecondary,
                        backgroundColor: function(context) {
                            return vm.getColor(context, '40');
                        },
                        hoverBorderWidth: 4,
                        radius: function(context) {
                            let radiusMin = 2;
                            let radius = Math.abs(context.raw?.induced_strength) / 2 || 1;

                            if (radius < radiusMin) {
                                return radiusMin;
                            }

                            return radius;
                        },
                    }
                },
                onClick: function(evt, el, chart) {
                    let activeElements = chart.getActiveElements();
                    if (activeElements.length > 0) {
                        let lastIndex = activeElements.length - 1;
                        let activeTooltip = activeElements[lastIndex].element?.$context?.raw;

                        vm.$emit('bubble-clicked', activeTooltip)
                    }
                },
                scales: {
                    x: {
                        suggestedMin: 0,
                        suggestedMax: 100,
                        title: {
                            display: true,
                            text: 'Semantic value',
                            color: '#aaa',
                        }

                    },
                    y: {
                        suggestedMin: 0,
                        suggestedMax: 100,
                        title: {
                            display: true,
                            text: 'Page value',
                            color: '#aaa',
                        },
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw?.source_url;
                            },
                        },
                    }
                }
            }
        },
        getData: function()
        {
            if (! this.source) {
                return null
            }

            return {
                datasets: [
                    {
                        data : this.source,
                        parsing: {
                            xAxisKey: 'semantic_value',
                            yAxisKey: 'page_value',
                        },
                    },
                ]
            }
        }
    },
}
</script>
