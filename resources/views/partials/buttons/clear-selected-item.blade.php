<button
    class="btn btn-primary btn-sm clear-selected-item-btn"
    title="{{ __('Clear selected item') }}"
    v-on:click="datasetCollectionStore.datasets.main.selected = null"
>x</button>
