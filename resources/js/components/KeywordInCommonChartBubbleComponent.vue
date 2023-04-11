<script>
import ChartBubbleComponent from '../components/ChartBubbleComponent'
const interpolate = require('color-interpolate');

export default {
    name: 'KeywordInCommonBubbleChart',
    extends: ChartBubbleComponent,

    methods: {
        getChartOptions: function() {
            let vm = this;
            return {
                responsive: true,
                elements: {
                    point: {
                        backgroundColor: vm.hexToRGB(vm.colorPrimary, 0.15),
                        borderColor: function(context) {
                            if (vm.selected && context.raw.id === vm.selected.id) {
                                return vm.colorSecondary;
                            }
                            return vm.hexToRGB(vm.colorPrimary, 0.50)
                        },
                        borderWidth: function(context) {
                            if (vm.selected && context.raw.id === vm.selected.id) {
                                return 3;
                            }

                            return 1;
                        },
                        hoverBackgroundColor: 'transparent',
                        hoverBorderColor: this.colorSecondary,
                        hoverBorderWidth: 4,
                        radius: function(context) {
                            const width = context.chart.width;
                            let radiusMin = 2;
                            let radiusMax = (width/6);
                            let base = Math.abs(context.raw?.nb_keywords_in_top20) / 10000 || 0.1;
                            let radius = (width / 32) * base;

                            if (radius > radiusMax) {
                                return radiusMax;
                            }

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
                            text: 'Host similarity (%)',
                            color: '#aaa',
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of common keywords in the top 20',
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
                            afterLabel: function(context) {
                                let nb = context.raw?.nb_keywords_in_top20 || 'n/a';
                                return nb + ' keywords';
                            },
                            label: function(context) {
                                return context.raw?.hostname;
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
                            xAxisKey: 'similar_score_percent',
                            yAxisKey: 'nb_keywords_in_common',
                        },
                    }
                ]
            }
        },
    },
}
</script>
