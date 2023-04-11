require('./bootstrap');
import {createPinia, mapStores, PiniaVuePlugin} from 'pinia'
import { useDatasetCollectionStore } from './stores/datasetCollection'
import Toasted from 'vue-toasted'
import VueSweetalert2 from 'vue-sweetalert2';
// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';

const Vue = require('vue').default
const EventBus = new Vue();
window.EventBus = EventBus;

Vue.use(PiniaVuePlugin)
Vue.use(VueSweetalert2)
Vue.use( Toasted, {
    duration: 5000,
    position: 'bottom-right'
})

const pinia = createPinia()

Vue.component('dataset', () => import('./components/DatasetComponent'))
Vue.component('dataset-filters', () => import('./components/DatasetFiltersComponent'))
Vue.component('dataset-action', () => import('./components/DatasetActionComponent'))
Vue.component('dataset-selection-toggle', () => import('./components/DatasetSelectionToggleComponent'))
Vue.component('dataset-collection', () => import('./components/DatasetCollectionComponent'))
Vue.component('keyword-in-common-chart-bubble', () => import('./components/KeywordInCommonChartBubbleComponent'))
Vue.component('backlink-chart-bubble', () => import('./components/BacklinkChartBubbleComponent'))
Vue.component('chart', () => import('./components/ChartComponent'))
Vue.component('screenshot', () => import('./components/ScreenshotComponent'))
Vue.component('dropdown', () => import('./components/DropdownComponent'))

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

window.app = new Vue({
    el: '#app',
    pinia,
    computed: {
        ...mapStores(useDatasetCollectionStore),
    },
});

// import vue-toasted to pinia instance
pinia.use(() => ({ $toasted: window.app.$toasted }))

// Popover
var myDefaultAllowList = bootstrap.Tooltip.Default.allowList
myDefaultAllowList.dl = []
myDefaultAllowList.dt = []
myDefaultAllowList.dd = []
const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl, {
    html: true,
    trigger: 'focus',
    content: function() {
        return document.getElementById(this.getAttribute('data-bs-target'))?.innerHTML || this.getAttribute('data-bs-content');
    }
}))
