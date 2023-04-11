<template>
    <div v-if="store" :id="'dataset-' + datasetId">
        <slot name="error" :error="store.error">
            <div class="alert alert-danger" role="alert" v-if="store.error">
                {{ store.error }}
            </div>
        </slot>

        <slot name="header" :store="store">
        </slot>

        <dataset-action v-if="actions"
            :actions="actions"
            :store="store"
            v-cloak
        ></dataset-action>

        <slot
            :store="store"
            :filters="store.payload.filters"
        >
        </slot>

        <slot name="loader" :loading="store.loading">
            <div class="loading" v-if="store.loading"></div>
        </slot>

        <slot name="footer" :store="store">
        </slot>
    </div>
</template>

<script>

import { useDatasetStore } from '../stores/dataset'
import { mapStores } from "pinia";
import { useDatasetCollectionStore } from "../stores/datasetCollection";
import boostrapHelpers from '../mixins/bootstrap-helpers'

    export default {
        mixins: [boostrapHelpers],
        props: {
            datasetId: {
                type: String,
                required: false,
                default: 'main'
            },
            api: {
                type: String,
                required: true
            },
            payload: {
                type: Object,
                required: false,
                default: () => {}
            },
            idle: {
                type: Boolean,
                required: false,
                default: false,
            },
            actions: {
                type: Array,
            }
        },

        data() {
            return {
                store: null
            }
        },
        watch: {
            'store.selected'(newSelected, oldSelected) {
                if (newSelected) {
                    this.$emit('selected', newSelected)
                }
            }
        },
        methods: {
            initStore() {
                this.store = useDatasetStore(this.datasetId)()
                this.store.init(this.api, this.payload)
                if (! this.idle) {
                    this.store.fetch();
                }

                this.datasetCollectionStore.add(this.datasetId, this.store)
            },
            foo() {
                console.log('foo fired')
                return 'bar'
            }
        },
        computed: {
            loaded() {
                return this.store?.loading === false
            },
            ...mapStores(useDatasetCollectionStore),
        },
        mounted() {
            console.log('Dataset ' + this.datasetId + ' mounted.')

            this.initStore()

            // Listen filtering event
            this.$on(`dataset-${this.datasetId}.filtering`, function(filters) {
                this.store.filtering(filters)
            })


        }
    }
</script>
