<template>
    <form @submit.prevent="storeInstance.filtering()" class="dataset-filters mb-3">
        <slot
            v-if="filters"
            :store="storeInstance"
            :filters="filters"
        />

        <slot name="buttons" :store="store">
            <div class="dataset-filters-button mt-4">
                <button class="btn btn-primary btn-sm">Filters</button>
                <button @click="storeInstance.resetFilters()" class="btn btn-link btn-sm">Reset</button>
            </div>
        </slot>
    </form>
</template>

<script>
import { mapStores } from 'pinia'
import { useDatasetCollectionStore } from '../stores/datasetCollection'

export default {
    name: 'DatasetFilters',
    props: {
        datasetId: {
            type: String,
            required: false,
            default: 'main'
        },
        store: {
            type: Object
        }
    },
    data() {
        return {
            storeInstance: this.store || null,
        }
    },
    computed: {
        ...mapStores(useDatasetCollectionStore),
        filters() {
            return this.storeInstance?.filters
        },
    },
    mounted () {
        let vm = this;

        this.datasetCollectionStore.$subscribe((mutation, state) => {
            if (!vm.storeInstance) {
                vm.storeInstance = vm.datasetCollectionStore.datasets[vm.datasetId]
            }
        })
    }
}
</script>
