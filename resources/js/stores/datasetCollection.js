import { defineStore } from 'pinia'

export const useDatasetCollectionStore = defineStore('datasetCollection', {
    state: () => ({
        datasets: {},
    }),
    getters: {
        get: (state) => {
            return (datasetId) => state.datasets[datasetId]
        },
        isLoading: (state) => {
            return (datasetId) => state.datasets[datasetId]?.loading || false
        },
        collection: (state) => {
            return function(datasetId, filterCallback) {
                let collection = state.datasets[datasetId]?.response?.data.map(a => ({...a})) || null

                if (collection && filterCallback) {
                    return collection.filter(filterCallback);
                }

                return collection;
            }
        },
        all: (state) => {
            return state.datasets;
        },
        count: (state) => {
            return Object.keys(state.datasets).length;
        }
    },
    actions: {
        add(datasetId, datasetStore)
        {
            // Todo: add warning if dataset already exist

            let newDatasets = {...this.datasets}
            newDatasets[datasetId] = datasetStore;

            this.datasets = newDatasets

            if (EventBus) {
                EventBus.$emit('dataset-added', datasetId)
            }
        },
    },
})
