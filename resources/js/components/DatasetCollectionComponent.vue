<template>
    <div>
        <form class="row g-3 align-items-center" v-on:submit.prevent="addDataset">
            <div class="col">
                <div class="row">
                    <label class="col-4 col-form-label" for="inlineFormSelectPref">Compare to competitor(s)</label>
                    <div class="col-8">
                        <select class="form-select" id="inlineFormSelectPref" v-model="source">
                            <option selected :value="null">Choose...</option>
                            <option :value="source" v-for="source in availableSources" :disabled="alreadyAdded(source.name)">
                                {{ source.name }}
                                <span v-if="source.complete === false">(data incomplete)</span>
                            </option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary" :disabled="!source">Add</button>
            </div>
        </form>
    </div>

</template>

<script>
import { mapStores } from 'pinia'
import { useDatasetCollectionStore } from '../stores/datasetCollection'
import { useDatasetStore } from "../stores/dataset";

export default {
    name: 'DatasetCollection',
    props: {
        sources : {
            type: Array,
            required: true,
        },
        payload: {
            type: Object,
            required: false,
        },
    },
    data: function() {
        return {
            source: null,
        }
    },
    computed: {
        ...mapStores(useDatasetCollectionStore),
        availableSources: function() {
            return this.sources;
        },
    },
    methods: {
        async addDataset() {
            if (! this.source) {
                return null;
            }

            // Create new dataset store and fetch data
            let store = useDatasetStore(this.source.name)()
            store.init(this.source.api, this.payload)
            await store.fetch();

            this.datasetCollectionStore.add(this.source.name, store)
            this.source = null;
            this.$emit('added', this.source);
        },
        alreadyAdded(datasetId) {
            return this.datasetCollectionStore.datasets[datasetId] !== undefined
        },
    },
}
</script>
